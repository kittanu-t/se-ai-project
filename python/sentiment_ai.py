from fastapi import FastAPI
from pydantic import BaseModel
from transformers import pipeline
import uvicorn

app = FastAPI()

# โหลดโมเดลครั้งเดียว (สำคัญมาก ⚠️)
model_name = "Thaweewat/wangchanberta-hyperopt-sentiment-01"
classifier = pipeline(
    "sentiment-analysis",
    model=model_name,
    tokenizer=model_name
)

class TextInput(BaseModel):
    text: str


@app.post("/analyze")
def analyze(data: TextInput):

    result = classifier(data.text)[0]

    label_map = {
        "LABEL_0": "negative",
        "LABEL_1": "positive",
        "LABEL_2": "neutral"
    }

    return {
        "status": "success",
        "label": label_map.get(result["label"]),
        "score": float(result["score"])
    }


if __name__ == "__main__":
    uvicorn.run(
        "sentiment_ai:app",
        host="127.0.0.1",
        port=8001,        
        reload=True
    )