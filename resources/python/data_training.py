import json
import os
import ast
import sys
import re
from textblob import TextBlob

# Mendapatkan argumen dari controller Laravel (data JSON)
json_data = sys.argv[1]

# Mencari nilai "namaUser" menggunakan regex
match = re.search(r'namaUser\s*:\s*([^}]+)', json_data)

namaUser_value = match.group(1).strip()
# print(namaUser_value)
# Membuat objek blob untuk mengevaluasi polarity sentimentnya
# Baca file JSON

json_file_path_data = os.path.abspath(os.path.join(os.path.dirname(__file__), '../json/'+namaUser_value+'/data_'+namaUser_value+'.json'))
with open(json_file_path_data, 'r', encoding="utf-8") as file:
    data = json.load(file)

# Fungsi untuk menentukan sentimen


def analyze_sentiment(text):
    analysis = TextBlob(text)
    if analysis.sentiment.polarity > 0:
        return 'positif'
    elif analysis.sentiment.polarity == 0:
        return 'netral'
    else:
        return 'negatif'


# Tambahkan label sentimen ke setiap entri
for entry in data:
    # Gantilah 'text' dengan kunci yang sesuai dalam struktur JSON Anda
    text = entry['text']
    sentiment = analyze_sentiment(text)
    entry['sentiment'] = sentiment

# Simpan data yang telah diberi label sentimen kembali ke file JSON
json_file_path_data_with_sentiment = os.path.abspath(os.path.join(os.path.dirname(__file__), '../json/'+namaUser_value+'/data_sentiment_'+namaUser_value+'.json'))
with open(json_file_path_data_with_sentiment, 'w') as file:
    json.dump(data, file, indent=4)

print("Label sentimen berhasil ditambahkan dan disimpan di 'data_sentiment_"+namaUser_value+".json'.")
