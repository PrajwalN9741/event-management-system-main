import pandas as pd
import glob
import os

# get absolute path of database folder
folder_path = os.path.join(os.path.dirname(__file__), "..", "database", "*.csv")

files = glob.glob(folder_path)

print("Files found:", files)

dataframes = []

for file in files:
    print("Loading:", file)
    df = pd.read_csv(file)
    dataframes.append(df)

merged_df = pd.concat(dataframes, ignore_index=True)

print("Merged Dataset Shape:", merged_df.shape)

merged_df.to_csv(os.path.join(os.path.dirname(__file__), "..", "database", "merged_dataset.csv"), index=False)

print("Dataset merged successfully!")