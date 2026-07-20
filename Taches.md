# Suivi des tâches - Projet Mobile Money

**Sujet** : Examen Projet Final - S4 Info et Design - Juillet 2026
**Binôme** :
- Côté opérateur : RAKOTONJANAHARY Hajaniaina Olivier
- Côté client : RAFALIMANANA Tsiory Fandresena

**Stack** : PHP (CodeIgniter 4), SQLite embarqué, HTML/CSS/JS, Bootstrap

---

## Version 1 (Tag : v1)

### Base de données (commun)

- [] Schéma `operateur`, `client`, `type_operation`, `bareme_frais`, `operation`
- [] Contrainte solde >= 0 sur `client`
- [] Fichier `base.sql` à la racine (tables + données)
- [] Migrations CodeIgniter correspondantes
- [] Seeder `InitialSeeder` (données de base)

### RAKOTONJANAHARY Hajaniaina Olivier — Côté opérateur

- [] Config préfixes opérateur (033, 037) - table `operateur`
- [] CRUD types d'opération (dépôt, retrait, transfert)
- [] CRUD barème de frais par tranche (modifiable, par opérateur + type)
- [] Fonction de calcul du frais selon montant + opérateur + type
- [] Écran gains par type de frais (retrait / transfert)
- [] Écran situation des comptes clients (liste + soldes)

### RAFALIMANANA Tsiory Fandresena — Côté client

- [] Route + vue login (formulaire numéro de téléphone)
    -[] Creation page login dans /client avec comme champ numero de tel seulement 
- [] Contrôleur login : vérifier préfixe valide
- [] Contrôleur login : créer client auto si numéro inconnu (solde 0)
- [] Session client connecté
- [] Écran solde
- [] Formulaire + traitement dépôt
- [] Formulaire + traitement retrait (vérifier solde suffisant)
- [] Formulaire + traitement transfert (vérifier solde suffisant + destinataire existe)
- [] Écran historique des opérations du client

---
