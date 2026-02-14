import sys
import json
import os
from transformers import pipeline

# ปิด Warning
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'

def analyze():
    try:
        if len(sys.argv) < 2:
            print(json.dumps({"status": "error", "message": "No text"}))
            return

        text = sys.argv[1]
        # ใช้โมเดล WangchanBERTa ที่คุณเลือก
        model_name = "Thaweewat/wangchanberta-hyperopt-sentiment-01"
        classifier = pipeline("sentiment-analysis", model=model_name, tokenizer=model_name)

        result = classifier(text)[0]

        # แปลง Label จากโมเดลเป็นคำอ่านง่าย
        label_map = {"LABEL_0": "negative", "LABEL_1": "positive", "LABEL_2": "neutral"}
        final_label = label_map.get(result['label'], result['label'])

        print(json.dumps({
            "status": "success",
            "label": final_label,
            "score": float(result['score'])
        }))
    except Exception as e:
        print(json.dumps({"status": "error", "message": str(e)}))

if __name__ == "__main__":
    analyze()