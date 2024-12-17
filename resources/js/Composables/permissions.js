import { usePage } from "@inertiajs/vue3";

export function usePermission() {
    const hasRole = (roles) => {
        const userRoles = usePage().props.auth?.roles || [];

        if (Array.isArray(roles)) {
            // Devuelve true si algún rol requerido está en los roles del usuario
            return roles.some(role => userRoles.includes(role));
        }

        // Devuelve true si el rol único requerido está en los roles del usuario
        return userRoles.includes(roles);
    };

    const hasPermission = (permissions) => {
        const userPermissions = usePage().props.auth?.permissions || [];

        if (Array.isArray(permissions)) {
            // Devuelve true si alguna permission requerida está en las permissions del usuario
            return permissions.some(permission => userPermissions.includes(permission));
        }

        // Devuelve true si la permission única requerida está en las permissions del usuario
        return userPermissions.includes(permissions);
    };

    return { hasRole, hasPermission };
}
