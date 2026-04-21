from __future__ import annotations

from statistics import mean
from typing import Any

from flask import Flask, jsonify, request


app = Flask(__name__)


def _reading(payload: dict[str, Any]) -> dict[str, Any]:
    reading = payload.get("reading")
    if isinstance(reading, dict):
        return reading

    return payload


def _number(value: Any, default: float = 0.0) -> float:
    try:
        return float(value)
    except (TypeError, ValueError):
        return default


@app.get("/status")
def status():
    return jsonify({"status": "ok", "service": "eoim-ai-engine"})


@app.post("/classify-device")
def classify_device():
    payload = request.get_json(silent=True) or {}
    name = str(payload.get("device_name") or payload.get("name") or "").lower()
    device_type = str(payload.get("device_type") or "").lower()
    text = f"{name} {device_type}"

    if any(token in text for token in ["air", "ac", "conditioner"]):
        category = "hvac"
    elif any(token in text for token in ["refrigerator", "fridge", "freezer"]):
        category = "cold_storage"
    elif any(token in text for token in ["washer", "washing", "machine"]):
        category = "appliance"
    elif any(token in text for token in ["light", "lamp", "led"]):
        category = "lighting"
    else:
        category = "general"

    return jsonify({
        "status": "success",
        "category": category,
        "confidence": 0.72 if category != "general" else 0.45,
    })


@app.post("/detect-anomaly")
def detect_anomaly():
    payload = request.get_json(silent=True) or {}
    reading = _reading(payload)

    voltage = _number(reading.get("voltage"))
    power = _number(reading.get("power"))
    power_factor = _number(reading.get("power_factor"), 1.0)
    current = _number(reading.get("current"))

    reasons: list[str] = []
    severity = "low"

    if voltage < 180 or voltage > 260:
        reasons.append("แรงดันไฟฟ้าอยู่นอกช่วงที่คาดไว้")
        severity = "critical"

    if power_factor < 0.5:
        reasons.append("ค่า Power Factor ต่ำกว่าค่าที่เหมาะสม")
        severity = "high" if severity != "critical" else severity

    if power > 3000:
        reasons.append("การใช้พลังงานสูงผิดปกติ")
        severity = "high" if severity != "critical" else severity

    if current > 20:
        reasons.append("กระแสไฟฟ้าสูงผิดปกติ")
        severity = "high" if severity != "critical" else severity

    is_anomaly = bool(reasons)

    return jsonify({
        "status": "success",
        "is_anomaly": is_anomaly,
        "severity": severity if is_anomaly else "normal",
        "message": " ".join(reasons) if reasons else "ค่าการทำงานอยู่ในช่วงปกติ",
    })


@app.post("/forecast-usage")
def forecast_usage():
    payload = request.get_json(silent=True) or {}
    readings = payload.get("readings")

    if not isinstance(readings, list) or not readings:
        return jsonify({
            "status": "success",
            "forecast": [],
            "message": "ยังไม่มีข้อมูลย้อนหลังสำหรับการคาดการณ์",
        })

    recent_energy = [_number(item.get("energy")) for item in readings[-12:] if isinstance(item, dict)]
    baseline = mean(recent_energy) if recent_energy else 0.0
    forecast = [{"period": index + 1, "energy": round(baseline, 3)} for index in range(6)]

    return jsonify({
        "status": "success",
        "forecast": forecast,
        "method": "moving_average",
    })


@app.post("/optimize-usage")
def optimize_usage():
    payload = request.get_json(silent=True) or {}
    reading = _reading(payload)
    power = _number(reading.get("power"))
    power_factor = _number(reading.get("power_factor"), 1.0)

    recommendations: list[str] = []
    if power > 3000:
        recommendations.append("ควรเลื่อนการใช้งานโหลดสูงไปยังช่วงเวลาที่มีภาระไฟฟ้าต่ำ")
    if power_factor < 0.8:
        recommendations.append("ควรตรวจสอบสมดุลโหลดและพิจารณาปรับปรุง Power Factor")
    if not recommendations:
        recommendations.append("รูปแบบการใช้งานปัจจุบันอยู่ในเกณฑ์ปกติและควรเก็บข้อมูลต่อเนื่อง")

    return jsonify({
        "status": "success",
        "recommendations": recommendations,
    })


if __name__ == "__main__":
    app.run(host="127.0.0.1", port=5000)
