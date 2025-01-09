import time
import board
import busio
from adafruit_bme280 import basic
import mysql.connector

# Créer un objet I2C
i2c = busio.I2C(board.SCL, board.SDA)

# Créer un objet BME280
bme280 = basic.Adafruit_BME280_I2C(i2c, address=0x76)  # Assurez-vous de vérifier l'adresse ici

# Connexion à la base de données MySQL
conn = mysql.connector.connect(
    host="localhost",
    user="meteo_user",   # Utilisateur de la base de données
    password="randompass", # Mot de passe de l'utilisateur
    database="meteo"     # Nom de la base de données
)
cursor = conn.cursor()

# Boucle pour lire les données et les insérer dans MySQL
try:
    while True:
        temperature = bme280.temperature
        humidite = bme280.humidity
        pression = bme280.pressure

        # Afficher les données du capteur
        print(f"Température: {temperature:.2f} °C | Humidité: {humidite:.2f} % | Pression: {pression:.2f} hPa")

        # Insertion des données dans la base de données
        query = "INSERT INTO mesures (temperature, humidite, pression) VALUES (%s, %s, %s)"
        cursor.execute(query, (temperature, humidite, pression))
        conn.commit()

        time.sleep(10)

except KeyboardInterrupt:
    print("Arrêt du programme.")

finally:
    cursor.close()
    conn.close()
