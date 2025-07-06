import { createRouter, createWebHistory } from 'vue-router'
import LoginForm from '@/components/LoginForm.vue'
import Home from '@/pages/Home.vue'
import RegisterForm from '@/components/RegisterForm.vue'
import Dashboard from '@/components/Dashboard.vue'
import MaisonPage from '@/pages/MaisonPage.vue'
import ChambrePage from '@/pages/ChambrePage.vue'
import ContratPage from '@/pages/ContratPage.vue'
import MediaPage from '@/pages/MediaPage.vue'
import PaiementPage from '@/pages/PaiementPage.vue'
import ProblemePage from '@/pages/ProblemePage.vue'
import RendezVousPage from '@/pages/RendezVousPage.vue'
import UtilisateurPage from '@/pages/UtilisateurPage.vue'
import ProfilePage from '@/pages/ProfilePage.vue'
import DashboardLoc from '@/components/DashboardLoc.vue'

import auth from '@/middleware/auth.js';



const routes = [
    { path: '/', component: Home },
    { path: '/login', component: LoginForm },
    { path: '/register', component: RegisterForm },
    
    { path: '/dashboard', component: Dashboard,beforeEnter: [auth] },
    { path: '/maisons', name: 'Maisons', component: MaisonPage},
    { path: '/chambres', name: 'Chambres', component: ChambrePage},
    { path: '/contrats', name: 'Contrats', component: ContratPage},
    { path: '/medias', name: 'Medias', component: MediaPage},
    { path: '/paiements', name: 'Paiements', component: PaiementPage},
    { path: '/problemes', name: 'Problemes', component: ProblemePage},
    { path: '/rendez-vous', name: 'RendezVous', component: RendezVousPage},
    { path: '/utilisateurs', name: 'Utilisateurs', component: UtilisateurPage},
    { path: '/profile', name: 'Profiles', component: ProfilePage, beforeEnter: [auth]},
    { path: '/dashboardLoc', component: DashboardLoc },

]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router
