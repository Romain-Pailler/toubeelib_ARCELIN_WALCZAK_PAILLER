<?php

namespace praticiens\core\services\authorization;

class ServiceAuthorization
{
    // Exemple de méthode isGranted qui vérifie si le rôle a accès à la ressource
    public function isGranted(string $role, string $resource): bool
    {
        // Récupère les permissions associées au rôle
        $rolePermissions = $this->getRolePermissions($role);

        // Vérifie si la ressource correspond à une route dynamique comme /praticiens/{id}
        foreach ($rolePermissions as $permission) {
            // Si la ressource correspond à /praticiens/{id}, l'autoriser
            if (preg_match('#^/praticiens(/.*)?$#', $resource)) {
                return true;
            }
        }

        // Vérifie les autres permissions (exactes)
        return in_array($resource, $rolePermissions);
    }


    // Cette méthode récupère les permissions associées à un rôle donné
    private function getRolePermissions(string $role): array
    {
        // Permissions de base pour les rôles
        $permissions = [
            'admin' => ['/praticiens', '/praticiens/{id}'],
            'praticien' => ['/praticiens', '/praticiens/{id}'],
        ];

        return $permissions[$role] ?? [];
    }
}
