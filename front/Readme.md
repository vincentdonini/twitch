/src
├── 📂 modules
│   ├── 📂 twitch (Module pour le domaine Twitch)
│   │   ├── 📂 application (Cas d'utilisation, logiques métiers)
│   │   ├── 📂 domain (Entités, objets de valeur)
│   │   ├── 📂 infrastructure (Intégrations API, services externes)
│   │   ├── 📂 ui (Composants React, hooks)
│   ├── 📂 user (Module pour gérer les utilisateurs)
│   ├── 📂 product (Module pour la gestion des produits)
│   ├── 📂 order (Module pour la gestion des commandes)
├── 📂 shared (Composants, utilitaires partagés)
│   ├── 📂 components (Composants réutilisables comme `Button`, `Modal`)
│   ├── 📂 utils (Fonctions utilitaires, helpers, formatters)
│   ├── 📂 services (Connexion API générique, gestion de l'état global)
│   ├── 📂 hooks (Hooks réutilisables comme `useAuth`, `useApi`)
├── 📂 app (Pages Next.js)
│   ├── index.tsx
│   ├── dashboard.tsx
│   ├── profile.tsx
└── 📂 public (Fichiers statiques)


Explication :
Modules : Chaque dossier sous modules/ correspond à un domaine métier (par exemple twitch, user, product). Chaque module peut contenir :

application/ : La logique métier, les cas d’utilisation (use cases).
domain/ : Entités (comme User, Product) et objets de valeur (comme FollowersCount).
infrastructure/ : Intégrations avec des APIs externes, services tiers (ex : l'API Twitch).
ui/ : Composants React spécifiques au module, ou des hooks liés à la logique métier du module.
Shared : Contient des ressources communes et réutilisables :

Composants : Composants UI réutilisables (ex : Button, Modal).
Services : Par exemple, un service d'authentification ou une configuration API.
Hooks : Hooks personnalisés comme useAuth ou useApi.
Pages : Représente les routes dans l'application Next.js, où chaque fichier est une page de l’application.




// TODO 
// ---------------------------------------------------------------------------------------------------------------------

# FEATURES
- Jeu ( duel )
- Entrainement ( WOD )
- Premier message dans le chat du jour
- KESKILEKOUT










