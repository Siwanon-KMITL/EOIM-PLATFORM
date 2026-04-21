# EOIM Python AI Service

Lightweight local AI service for the EOIM PHP platform.

## Setup

From the project root:

```powershell
python -m venv .venv
.venv\Scripts\activate
pip install -r requirements.txt
python app.py
```

Or from this `python_ai` directory:

```powershell
python -m venv .venv
.venv\Scripts\activate
pip install -r requirements.txt
python app.py
```

The PHP application calls this service through `PYTHON_API_URL`, which defaults to:

```text
http://127.0.0.1:5000
```

## Endpoints

- `GET /status`
- `POST /classify-device`
- `POST /detect-anomaly`
- `POST /forecast-usage`
- `POST /optimize-usage`
