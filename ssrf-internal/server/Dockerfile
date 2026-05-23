FROM python:3.11-slim

LABEL maintainer="ZhaoSec <security@zhaosec.com>"
LABEL description="SSRF Cloud Metadata Server - Python Flask"

WORKDIR /app

COPY requirements.txt .

RUN pip install --no-cache-dir -r requirements.txt

COPY app.py /app/

EXPOSE 80

CMD ["python", "app.py"]
