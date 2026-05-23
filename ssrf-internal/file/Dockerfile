FROM python:3.11-slim

LABEL maintainer="ZhaoSec <security@zhaosec.com>"
LABEL description="SSRF Internal File Server - Python Flask"

WORKDIR /app

COPY web/requirements.txt .

RUN pip install --no-cache-dir -r requirements.txt

COPY web/ /app/

EXPOSE 80

CMD ["python", "file.py"]
