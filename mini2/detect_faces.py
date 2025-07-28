import sys, json
from pathlib import Path

folder = sys.argv[1]

# Simulate detected output
result = {
    "recognized": ["ENR001", "ENR003"],
    "unknown_imgs": [
        f"{folder}/unknown_1.jpg",
        f"{folder}/unknown_2.jpg"
    ]
}

with open(f"{folder}/output.json", "w") as f:
    json.dump(result, f)

print(json.dumps(result))
