#!/bin/bash
set -e

# Create databases
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
    CREATE DATABASE praticien;
EOSQL

# Insert data into each database

# Insert data into Praticien DB
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "praticien" <<-EOSQL
    CREATE TABLE Specialite (
        id VARCHAR(255) PRIMARY KEY,
        label VARCHAR(100) NOT NULL,
        description TEXT
    );
    CREATE TABLE Praticien (
        id VARCHAR(255) PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        prenom VARCHAR(100) NOT NULL,
        adresse VARCHAR(255) NOT NULL,
        tel VARCHAR(20) NOT NULL,
        specialite_id VARCHAR(255),
        FOREIGN KEY (specialite_id) REFERENCES Specialite(id)
    );
    INSERT INTO Specialite (id, label, description) VALUES 
        ('A','Cardiologie', 'Spécialité médicale dédiée aux troubles du cœur.'),
        ('B','Dermatologie', 'Spécialité consacrée aux maladies de la peau.'),
        ('C','Ophtalmologie', 'Spécialité des troubles de la vision.');
    INSERT INTO Praticien (id, nom, prenom, adresse, tel, specialite_id) VALUES 
        ('1','Dupont', 'Marie', '10 rue de la République, 54000 Nancy', '0601234567', 'A'),
        ('2','Martin', 'Jean', '12 avenue des Vosges, 57000 Metz', '0609876543', 'B'),
        ('3','Durand', 'Luc', '5 boulevard Saint-Michel, 75005 Paris', '0601112233', 'C');
EOSQL


