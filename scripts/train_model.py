import pandas as pd
import joblib
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import LabelEncoder
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import classification_report

# ---------------------------
# Load Dataset
# ---------------------------

df = pd.read_csv("dataset.csv")

print("Dataset Loaded:", df.shape)

# ---------------------------
# Data Cleaning
# ---------------------------

df = df.dropna()

# Convert label to binary
df["Label"] = df["Label"].apply(lambda x: 0 if x == "BENIGN" else 1)

# Select numeric features
X = df.select_dtypes(include=["int64","float64"])

# Remove label from features
X = X.drop(columns=["Label"], errors="ignore")

y = df["Label"]

# ---------------------------
# Train Test Split
# ---------------------------

X_train, X_test, y_train, y_test = train_test_split(
    X, y, test_size=0.2, random_state=42
)

# ---------------------------
# Train Model
# ---------------------------

model = RandomForestClassifier(
    n_estimators=150,
    max_depth=10,
    random_state=42
)

model.fit(X_train, y_train)

# ---------------------------
# Evaluate Model
# ---------------------------

pred = model.predict(X_test)

print("\nModel Evaluation\n")
print(classification_report(y_test, pred))

# ---------------------------
# Save Model
# ---------------------------

joblib.dump(model, "soc_model.pkl")

print("\nModel saved as soc_model.pkl")