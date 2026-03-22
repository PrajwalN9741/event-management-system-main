import pandas as pd
import numpy as np
import os

# path to merged dataset
file_path = os.path.join(os.path.dirname(__file__), "..", "database", "merged_dataset.csv")

print("Loading dataset...")

df = pd.read_csv(file_path)

print("Original Shape:", df.shape)

# remove spaces in column names
df.columns = df.columns.str.strip()

# replace infinity values
df.replace([np.inf, -np.inf], np.nan, inplace=True)

# drop missing values
df.dropna(inplace=True)

# remove duplicate rows
df.drop_duplicates(inplace=True)

print("Cleaned Shape:", df.shape)

# save cleaned dataset
clean_path = os.path.join(os.path.dirname(__file__), "..", "database", "cleaned_dataset.csv")

df.to_csv(clean_path, index=False)

print("Cleaned dataset saved successfully!")