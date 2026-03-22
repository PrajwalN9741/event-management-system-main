import pandas as pd
import os
import joblib
import random
import time
from datetime import datetime

# Load ML model
model_path = os.path.join(os.path.dirname(__file__), "..", "database", "rf_model.pkl")
model = joblib.load(model_path)

# Load dataset
data_path = os.path.join(os.path.dirname(__file__), "..", "database", "cleaned_dataset.csv")
df = pd.read_csv(data_path)

df.columns = df.columns.str.strip()

# Risk scoring rules
risk_scores = {
    "BENIGN": 10,
    "PortScan": 50,
    "Bot": 70,
    "DDoS": 90,
    "DoS Hulk": 85,
    "DoS GoldenEye": 85,
    "DoS slowloris": 80,
    "DoS Slowhttptest": 80,
    "FTP-Patator": 75,
    "SSH-Patator": 75,
    "Web Attack – Brute Force": 65,
    "Web Attack – XSS": 60,
    "Web Attack – Sql Injection": 95,
    "Infiltration": 90,
    "Heartbleed": 100
}

# Simulated SOC threats
simulated_attacks = [
    "Ransomware Activity",
    "Data Exfiltration",
    "Privilege Escalation",
    "Malware Beaconing",
    "Phishing Attempt"
]

# MITRE Mapping
mitre_mapping = {
    "DDoS": "Impact",
    "PortScan": "Reconnaissance",
    "Bot": "Command and Control",
    "FTP-Patator": "Credential Access",
    "SSH-Patator": "Credential Access",
    "Web Attack – Sql Injection": "Initial Access",
    "Web Attack – XSS": "Initial Access",
    "Web Attack – Brute Force": "Credential Access",
    "Infiltration": "Lateral Movement",
    "Heartbleed": "Credential Access",
    "Ransomware Activity": "Impact",
    "Data Exfiltration": "Exfiltration",
    "Privilege Escalation": "Privilege Escalation",
    "Malware Beaconing": "Command and Control",
    "Phishing Attempt": "Initial Access"
}

log_path = os.path.join(os.path.dirname(__file__), "..", "database", "alerts_log.csv")

print("\nSOC Alert Generator Running...\n")

alert_id = 1

while True:

    # Decide ML or simulated attack
    if random.random() < 0.7:
        sample = df.sample(1)
        X = sample.drop("Label", axis=1)
        attack = model.predict(X)[0]
    else:
        attack = random.choice(simulated_attacks)

    score = risk_scores.get(attack, random.randint(70,95))

    if score >= 90:
        priority = "Critical"
    elif score >= 70:
        priority = "High"
    elif score >= 50:
        priority = "Medium"
    else:
        priority = "Low"

    mitre = mitre_mapping.get(attack, "Unknown")

    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")

    alert = pd.DataFrame([[alert_id, attack, score, priority, mitre, timestamp]],
                         columns=["AlertID","Attack","RiskScore","Priority","MITRE_Technique","Timestamp"])

    if os.path.exists(log_path):
        alert.to_csv(log_path, mode="a", header=False, index=False)
    else:
        alert.to_csv(log_path, index=False)

    print(f"Alert {alert_id}: {attack} | {priority}")

    alert_id += 1

    time.sleep(2)