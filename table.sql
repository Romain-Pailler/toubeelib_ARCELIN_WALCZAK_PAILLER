CREATE DATABASE praticiens_db;
CREATE DATABASE rdv_db;
CREATE DATABASE patients_db;
CREATE DATABASE auth_db;


CREATE TABLE Praticien (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    adresse VARCHAR(255) NOT NULL,
    tel VARCHAR(20) NOT NULL,
    specialite_id INTEGER,
    FOREIGN KEY (specialite_id) REFERENCES Specialite(id)
);

CREATE TABLE Specialite (
    id SERIAL PRIMARY KEY,
    label VARCHAR(100) NOT NULL,
    description TEXT
);

CREATE TABLE RendezVous (
    id SERIAL PRIMARY KEY,
    praticien_id INTEGER NOT NULL,
    patient_id INTEGER NOT NULL,
    specialite_id INTEGER,
    statut VARCHAR(50) DEFAULT 'prévu',
    date TIMESTAMP NOT NULL,
    FOREIGN KEY (praticien_id) REFERENCES Praticien(id),
    FOREIGN KEY (patient_id) REFERENCES Patients(id),
    FOREIGN KEY (specialite_id) REFERENCES Specialite(id)
);

CREATE TABLE Patients (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    adresse VARCHAR(255) NOT NULL,
    tel VARCHAR(20) NOT NULL,
    date_naissance DATE
);

