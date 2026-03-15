# ใช้ slim สำหรับ CPU-only inference (Railway free tier ไม่มี GPU)
FROM python:3.11-slim

WORKDIR /app

# Install system dependencies ที่ torch/transformers ต้องการ
RUN apt-get update && apt-get install -y --no-install-recommends \
    gcc \
    g++ \
    libgomp1 \
    && rm -rf /var/lib/apt/lists/*

# ── Step 1: Install torch CPU-only แยกก่อน ────────────────
# torch+cpu ขนาด ~800MB vs torch+cuda ~2.5GB
# Railway free tier ไม่มี GPU อยู่แล้ว ใช้ CPU version พอ
RUN pip install --no-cache-dir \
    torch==2.3.1+cpu \
    --index-url https://download.pytorch.org/whl/cpu

# ── Step 2: Install ส่วนที่เหลือ ──────────────────────────
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# ── Step 3: Copy app files ─────────────────────────────────
COPY . .

# ── (Optional) Pre-download Hugging Face model ────────────
# uncomment + แก้ชื่อ model ให้ตรง ถ้าต้องการ cache ไว้ใน image
# RUN python -c "from transformers import AutoTokenizer, AutoModel; \
#     AutoTokenizer.from_pretrained('YOUR_MODEL_NAME'); \
#     AutoModel.from_pretrained('YOUR_MODEL_NAME')"

EXPOSE 8001

# gunicorn: production server, timeout 120s สำหรับ ML inference ที่อาจใช้เวลา
CMD ["gunicorn", "--bind", "0.0.0.0:8001", "--workers", "1", "--timeout", "120", "app:app"]