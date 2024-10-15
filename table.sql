--CREATE DATABASE praticiens_db;
--CREATE DATABASE rdv_db;
--CREATE DATABASE patients_db;
--CREATE DATABASE auth_db;
--JSP si on va utiliser les lignes au dessus

--Il faut se connecter au serveur PostgreSQL pour créer la base de données donc avec adminer rentrer les identifiants propres au Praticien / Patient / RDV 


--Praticien DB

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


--RDV DB PostgreSQL FDW

CREATE EXTENSION postgres_fdw;

CREATE SERVER praticien_server
    FOREIGN DATA WRAPPER postgres_fdw
    OPTIONS (host 'praticien.db', dbname 'praticien', port '5433');

CREATE USER MAPPING FOR current_user
    SERVER praticien_server
    OPTIONS (user 'praticien_user', password 'praticien_password');

CREATE FOREIGN TABLE praticien (
    id SERIAL,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    adresse VARCHAR(255),
    tel VARCHAR(20),
    specialite_id INTEGER
)
SERVER praticien_server
OPTIONS (schema_name 'public', table_name 'Praticien');


CREATE SERVER patient_server
    FOREIGN DATA WRAPPER postgres_fdw
    OPTIONS (host 'patient.db', dbname 'patients', port '5432');

CREATE USER MAPPING FOR current_user
    SERVER patient_server
    OPTIONS (user 'patient_user', password 'patient_password');

CREATE FOREIGN TABLE patient (
    id SERIAL,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    adresse VARCHAR(255),
    tel VARCHAR(20),
    date_naissance DATE
)
SERVER patient_server
OPTIONS (schema_name 'public', table_name 'Patients');


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
    --PB avec les foreign key 
);


--Patients DB

CREATE TABLE Patients (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    adresse VARCHAR(255) NOT NULL,
    tel VARCHAR(20) NOT NULL,
    date_naissance DATE
);

