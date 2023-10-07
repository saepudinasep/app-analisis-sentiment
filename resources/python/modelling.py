import json
import os
import sys
import re
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.model_selection import train_test_split
from sklearn.naive_bayes import MultinomialNB  # Model Naive Bayes
from sklearn.svm import SVC  # Model SVM
from sklearn.ensemble import RandomForestClassifier  # Model Random Forest
from sklearn.neighbors import KNeighborsClassifier  # Model K-Nearest Neighbors
from sklearn.linear_model import LogisticRegression  # Model Logistic Regression
from sklearn.metrics import accuracy_score, classification_report


# Mendapatkan argumen dari controller Laravel (data JSON)
json_data = sys.argv[1]

# Mencari nilai "namaUser" menggunakan regex
match = re.search(r'namaUser\s*:\s*([^}]+)', json_data)

namaUser_value = match.group(1).strip()
# print(namaUser_value)


# Baca data dari 'data_sentiment_'+namaUser_value+'.json'
json_file_path_data_with_sentiment = os.path.abspath(os.path.join(os.path.dirname(__file__), '../json/'+namaUser_value+'/data_sentiment_'+namaUser_value+'.json'))
with open(json_file_path_data_with_sentiment, 'r') as file:
    data = json.load(file)

# Konversi label sentimen menjadi nilai numerik
label_mapping = {'positif': 1, 'netral': 0, 'negatif': -1}
for entry in data:
    entry['sentiment'] = label_mapping[entry['sentiment']]

# Pisahkan data menjadi fitur (teks) dan label (sentimen)
X = [entry['text'] for entry in data]
y = [entry['sentiment'] for entry in data]

# Bagi data menjadi data latih dan data uji
X_train, X_test, y_train, y_test = train_test_split(
    X, y, test_size=0.2, random_state=42)

# Data latih
train_data = {'X_train': X_train, 'y_train': y_train}

json_file_path_train_data = os.path.abspath(os.path.join(os.path.dirname(__file__), '../json/'+namaUser_value+'/train_data_'+namaUser_value+'.json'))
# Simpan data latih ke dalam file JSON
with open(json_file_path_train_data, 'w') as train_file:
    json.dump(train_data, train_file, indent=4)

# Data uji
test_data = {'X_test': X_test, 'y_test': y_test}

json_file_path_test_data = os.path.abspath(os.path.join(os.path.dirname(__file__), '../json/'+namaUser_value+'/test_data_'+namaUser_value+'.json'))
# Simpan data uji ke dalam file JSON
with open(json_file_path_test_data, 'w') as test_file:
    json.dump(test_data, test_file, indent=4)

# Vektorisasi teks (TF-IDF)
tfidf_vectorizer = TfidfVectorizer(max_features=5000)
X_train_tfidf = tfidf_vectorizer.fit_transform(X_train)
X_test_tfidf = tfidf_vectorizer.transform(X_test)

# Inisialisasi model-model
models = {
    'Naive Bayes': MultinomialNB(),  # Model Naive Bayes
    'SVM': SVC(),  # Model SVM
    # Model Random Forest
    'Random Forest': RandomForestClassifier(n_estimators=100, random_state=42),
    'KNN': KNeighborsClassifier(n_neighbors=5),  # Model K-Nearest Neighbors
    # Model Logistic Regression
    'Logistic Regression': LogisticRegression(max_iter=1000)
}

# Membuat dictionary untuk menyimpan hasil klasifikasi untuk setiap model
results = {}

# Latih dan evaluasi setiap model
for model_name, model in models.items():
    model.fit(X_train_tfidf, y_train)
    y_pred = model.predict(X_test_tfidf)

    accuracy = accuracy_score(y_test, y_pred)
    classification_rep = classification_report(
        y_test, y_pred, target_names=label_mapping.keys(), zero_division=0)

    # Simpan hasil evaluasi ke dalam file JSON
    evaluation_result = {
        'accuracy': accuracy,
        'classification_report': classification_rep
    }

    # Construct the absolute file path for saving the JSON file
    json_file_path = os.path.abspath(os.path.join(os.path.dirname(__file__), f'../json/'+namaUser_value+'/'+model_name+'_evaluation_'+namaUser_value+'.json'))
    # Construct the absolute file path for saving the JSON file
    json_file_result = os.path.abspath(os.path.join(os.path.dirname(__file__), f'../json/'+namaUser_value+'/'+model_name+'_classification_'+namaUser_value+'.json'))

    with open(json_file_path, 'w') as json_file:
        json.dump(evaluation_result, json_file, indent=4)


    # Ganti nilai numerik dalam y_test dengan label sentimen
    y_test_sentiment = [key for val in y_test for key,
                        value in label_mapping.items() if value == val]

    # Ganti nilai numerik dalam y_pred dengan label sentimen
    y_pred_sentiment = [key for val in y_pred for key,
                        value in label_mapping.items() if value == val]

    # Buat DataFrame pandas untuk menampilkan hasil klasifikasi dalam bentuk tabel
    classification_df = pd.DataFrame({
        'text': X_test,
        'sentiment': y_test_sentiment,
        'predict': y_pred_sentiment
    })

    # Simpan tabel hasil klasifikasi ke dalam file JSON
    classification_df.to_json(json_file_result, orient='records')


# print("Berhasil Modelling")
# print(classification_rep)
# # Tampilkan hasil evaluasi
# for model_name in models.keys():

#     # Construct the absolute file path for opening the JSON file
#     json_file_evaluation = os.path.abspath(os.path.join(os.path.dirname(__file__), f'../json/{model_name}_evaluation.json'))

#     with open(json_file_evaluation, 'r') as json_file:
#         evaluation_result = json.load(json_file)
#     print(f'{model_name} Model:')
#     print(f'Accuracy: {evaluation_result["accuracy"]:.2f}')
#     print('Classification Report:')
#     print(evaluation_result['classification_report'])
#     print('-' * 50)
