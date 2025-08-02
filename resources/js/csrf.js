// csrf.js - Helper to initialize CSRF cookie for Laravel Sanctum SPA

import api from './api';

export async function initializeCsrfCookie() {
    try {
        await api.get('/sanctum/csrf-cookie');
        console.log('CSRF cookie initialized');
    } catch (error) {
        console.error('Failed to initialize CSRF cookie', error);
    }
}
