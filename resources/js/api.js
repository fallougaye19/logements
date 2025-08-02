// // api.js - Configuration centralisée d'Axios avec debug amélioré
import axios from 'axios';
import router from './router';

const api = axios.create({
    baseURL: 'http://localhost:8000',
    withCredentials: true,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    }
});

const getToken = () => {
    return localStorage.getItem('token') || localStorage.getItem('auth_token');
};

// Fonction pour nettoyer l'authentification
const clearAuth = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
};

// Intercepteur pour les requêtes - Debug détaillé
api.interceptors.request.use(
    (config) => {
        const token = getToken();

        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }

        // Log détaillé pour débugger
        if (process.env.NODE_ENV === 'development') {
            console.group(`${config.method.toUpperCase()} ${config.url}`);
            console.log('Full URL:', `${config.baseURL}${config.url}`);
            console.log('Headers:', config.headers);
            console.log('Data:', config.data);
            console.log('Token present:', !!token);
            console.log('Token preview:', token ? `${token.substring(0, 20)}...` : 'None');
            console.groupEnd();
        }
        return config;
    },
    (error) => {
        console.error('Erreur dans la requête:', error);
        return Promise.reject(error);
    }
);

// Intercepteur pour les réponses - Debug détaillé
api.interceptors.response.use(
    (response) => {
        if (process.env.NODE_ENV === 'development') {
            console.group(`${response.status} ${response.config.method.toUpperCase()} ${response.config.url}`);

            // Vérification si on reçoit du HTML au lieu du JSON attendu
            if (typeof response.data === 'string' && response.data.includes('<!DOCTYPE html>')) {
                console.warn(' Réponse HTML inattendue reçue - possible problème de routage Laravel');
                console.log('Response data preview:', response.data.substring(0, 200) + '...');
            } else {
                console.log('Response data:', response.data);
            }

            console.groupEnd();
        }
        return response;
    },
    async (error) => {
        if (process.env.NODE_ENV === 'development') {
            console.group(`Error ${error.response?.status || 'Network'} ${error.config?.method?.toUpperCase() || 'Unknown'} ${error.config?.url || 'Unknown URL'}`);

            if (error.response) {
                console.log('Status:', error.response.status);
                console.log('Status Text:', error.response.statusText);
                console.log('Response Headers:', error.response.headers);

                // Affichage intelligent des données d'erreur
                if (typeof error.response.data === 'string' && error.response.data.includes('<!DOCTYPE html>')) {
                    console.log('HTML Error Response (preview):', error.response.data.substring(0, 300) + '...');
                } else {
                    console.log('Response Data:', error.response.data);
                }

                console.log('Request URL:', error.config.url);
                console.log('Request Method:', error.config.method);
                console.log('Request Data:', error.config.data);
            } else if (error.request) {
                console.log('Network Error - No response received');
                console.log('Request:', error.request);
            } else {
                console.log('Error Message:', error.message);
            }

            console.groupEnd();
        }
        if (error.response) {
            const status = error.response.status;

            switch (status) {
                case 401:
                    console.log('Token expiré ou invalide');
                    clearAuth();

                    // Éviter les boucles de redirection
                    if (router.currentRoute.value.path !== '/login') {
                        router.push('/login');
                    }
                    break;

                case 403:
                    console.log('Accès refusé');
                    // Optionnel: afficher une notification à l'utilisateur
                    break;

                case 404:
                    console.log('Ressource non trouvée');
                    console.log('URL tentée:', error.config.url);
                    break;

                case 405:
                    console.log('Méthode non autorisée - Vérifiez vos routes backend');
                    console.log('URL appelée:', error.config.url);
                    console.log('Méthode utilisée:', error.config.method?.toUpperCase());
                    break;

                case 419: // Token CSRF manquant/expiré
                    console.log('Token CSRF manquant ou expiré');
                    // Pour les apps SPA, essayer de récupérer un nouveau token CSRF
                    break;

                case 422:
                    console.log('Erreur de validation');
                    if (error.response.data?.errors) {
                        console.table(error.response.data.errors);
                    }
                    break;

                case 500:
                    console.log('Erreur serveur interne');
                    break;

                default:
                    console.log(`Erreur HTTP: ${status}`);
            }
        }

        return Promise.reject(error);
    }
);

export const initializeCSRF = async () => {
    try {
        await api.get('/sanctum/csrf-cookie');
        console.log('CSRF token initialisé');
        return true;
    } catch (error) {
        console.error('Erreur lors de l\'initialisation CSRF:', error);
        return false;
    }
};

// Fonction utilitaire pour tester la connexion API
export const testApiConnection = async () => {
    try {
        const response = await api.get('/user'); // ou une route de test
        console.log('Connexion API OK');
        return { success: true, data: response.data };
    } catch (error) {
        console.error('Test de connexion API échoué');
        return { success: false, error };
    }
};

export default api;
