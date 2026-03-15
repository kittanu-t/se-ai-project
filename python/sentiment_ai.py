from flask import Flask, request, jsonify
from transformers import pipeline
import os

app = Flask(__name__)

#  โหลดโมเดลครั้งเดียว 
model_name = "Thaweewat/wangchanberta-hyperopt-sentiment-01"

classifier = pipeline(
    "sentiment-analysis",
    model=model_name,
    tokenizer=model_name
)

label_map = {
    "LABEL_0": "negative",
    "LABEL_1": "positive",
    "LABEL_2": "neutral"
}


@app.route("/analyze", methods=["POST"])
def analyze():

    data = request.get_json()

    if not data or "text" not in data:
        return jsonify({
            "status": "error",
            "message": "No text provided"
        }), 400

    text = data["text"]

    result = classifier(text)[0]

    return jsonify({
        "status": "success",
        "label": label_map.get(result["label"]),
        "score": float(result["score"])
    })


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=int(os.environ.get('PORT', 8001)), debug=False)