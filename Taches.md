# Suivi des tâches - Projet Mobile Money

**Sujet** : Examen Projet Final - S4 Info et Design - Juillet 2026
**Binôme** :
- Côté opérateur : RAKOTONJANAHARY Hajaniaina Olivier
- Côté client : RAFALIMANANA Tsiory Fandresena

**Stack** : PHP (CodeIgniter 4), SQLite embarqué, HTML/CSS/JS, Bootstrap

---

## Version 1 (Tag : v1)

### Base de données (commun)

- [ok] Schéma `operateur`, `client`, `type_operation`, `bareme_frais`, `operation`
- [ok] Contrainte solde >= 0 sur `client`
- [ok] Fichier `base.sql` à la racine (tables + données)
- [ok] Migrations CodeIgniter correspondantes
- [ok] Seeder `InitialSeeder` (données de base)

### RAKOTONJANAHARY Hajaniaina Olivier — Côté opérateur

- [ok] Config préfixes opérateur (033, 037) - table `operateur`
- [ok] CRUD types d'opération (dépôt, retrait, transfert)
- [ok] CRUD barème de frais par tranche (modifiable, par opérateur + type)
- [ok] Fonction de calcul du frais selon montant + opérateur + type
- [ok] Écran gains par type de frais (retrait / transfert)
- [ok] Écran situation des comptes clients (liste + soldes)

### RAFALIMANANA Tsiory Fandresena — Côté client

- [ok] Route + vue login (formulaire numéro de téléphone)
    -[ok] Creation page login dans /client avec comme champ numero de tel seulement 
- [ok] Contrôleur login : vérifier préfixe valide
    - [ok] erreur si prefixe invalide 
- [ok] Contrôleur login : créer client auto si numéro inconnu (avec solde 0)
- [ok] Session client connecté
- [ok] Écran solde
- [ok] Formulaire + traitement dépôt
- [ok] Formulaire + traitement retrait (vérifier solde suffisant)
- [ok] Formulaire + traitement transfert (vérifier solde suffisant + destinataire existe)
- [ok] Écran historique des opérations du client

---
