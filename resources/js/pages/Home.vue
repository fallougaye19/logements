<template>
    <div class="home">
        <header id="header">
            <a href="#" class="logo">
                <i class="fas fa-building logo-icon"></i>
                <span class="logo-text">RésidencePlus</span>
            </a>

            <nav>
                <ul>
                    <li><a href="#hero">Accueil</a></li>
                    <li><a href="#proprietes">Propriétés</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#temoignages">À propos</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </nav>

            <div class="auth-buttons">
                <button class="btn btn-dark" @click="goToLogin">
                    <i class="fas fa-sign-in-alt"></i> Connexion
                </button>
                <button class="btn btn-info" @click="goToRegister">
                    <i class="fas fa-user-plus"></i> Inscription
                </button>
            </div>
        </header>

        <section class="hero" id="hero">
            <div class="hero-content">
                <h1>
                    Gérez vos <span>locations immobilières</span> avec facilité
                </h1>
                <p>
                    RésidencePlus est la solution tout-en-un pour la gestion de
                    vos appartements et chambres en location. Simplifiez la
                    gestion des locataires, des paiements et de la maintenance
                    avec notre plateforme intuitive.
                </p>

                <div class="hero-buttons">
                    <button class="btn btn-primary" @click="goToRegister">
                        <i class="fas fa-play-circle"></i> Commencer maintenant
                    </button>
                    <button class="btn btn-secondary">
                        <i class="fas fa-info-circle"></i> En savoir plus
                    </button>
                </div>
            </div>
        </section>

        <section class="properties" id="proprietes">
            <h2 class="section-title animate-on-scroll">Nos Propriétés</h2>
            <p class="section-subtitle animate-on-scroll">
                Découvrez nos appartements et chambres d'hôtel exceptionnels
                disponibles à la location
            </p>

            <div class="property-cards">
                <!-- Appartement Paris -->
                <div class="property-card animate-on-scroll">
                    <span class="property-badge">Disponible</span>
                    <img
                        src="https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                        alt="Appartement Paris"
                        class="property-img"
                    />
                    <div class="card-info">
                        <h3>Appartement à Paris</h3>
                        <p>
                            Élégant appartement de 2 chambres en plein cœur de
                            Paris, proche de toutes commodités.
                        </p>

                        <div class="property-features">
                            <div class="feature">
                                <i class="fas fa-bed"></i> 2 chambres
                            </div>
                            <div class="feature">
                                <i class="fas fa-bath"></i> 1 salle de bain
                            </div>
                            <div class="feature">
                                <i class="fas fa-ruler-combined"></i> 65 m²
                            </div>
                        </div>

                        <span class="price">€850<span>/mois</span></span>
                        <button class="btn btn-primary">
                            Réserver maintenant
                        </button>
                    </div>
                </div>

                <!-- Chambre d'hôtel Lyon -->
                <div class="property-card animate-on-scroll">
                    <span class="property-badge">Disponible</span>
                    <img
                        src="https://images.unsplash.com/photo-1566665797739-1674de7a421a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                        alt="Chambre Lyon"
                        class="property-img"
                    />
                    <div class="card-info">
                        <h3>Chambre d'hôtel à Lyon</h3>
                        <p>
                            Chambre moderne avec vue sur la ville dans un hôtel
                            4 étoiles, petit-déjeuner inclus.
                        </p>

                        <div class="property-features">
                            <div class="feature">
                                <i class="fas fa-bed"></i> 1 lit king size
                            </div>
                            <div class="feature">
                                <i class="fas fa-wifi"></i> Wifi gratuit
                            </div>
                            <div class="feature">
                                <i class="fas fa-utensils"></i> Petit-déjeuner
                            </div>
                        </div>

                        <span class="price">€95<span>/nuit</span></span>
                        <button class="btn btn-primary">
                            Réserver maintenant
                        </button>
                    </div>
                </div>

                <!-- Studio Marseille -->
                <div class="property-card animate-on-scroll">
                    <span class="property-badge">Disponible</span>
                    <img
                        src="https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                        alt="Studio Marseille"
                        class="property-img"
                    />
                    <div class="card-info">
                        <h3>Studio à Marseille</h3>
                        <p>
                            Studio lumineux avec vue sur le Vieux-Port,
                            entièrement meublé et équipé.
                        </p>

                        <div class="property-features">
                            <div class="feature">
                                <i class="fas fa-home"></i> Studio
                            </div>
                            <div class="feature">
                                <i class="fas fa-wind"></i> Climatisation
                            </div>
                            <div class="feature">
                                <i class="fas fa-parking"></i> Parking
                            </div>
                        </div>

                        <span class="price">€650<span>/mois</span></span>
                        <button class="btn btn-primary">
                            Réserver maintenant
                        </button>
                    </div>
                </div>

                <!-- Suite Bordeaux -->
                <div class="property-card animate-on-scroll">
                    <span class="property-badge">Disponible</span>
                    <img
                        src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                        alt="Suite Bordeaux"
                        class="property-img"
                    />
                    <div class="card-info">
                        <h3>Suite à Bordeaux</h3>
                        <p>
                            Suite luxueuse dans un hôtel de charme avec spa,
                            idéale pour un séjour romantique.
                        </p>

                        <div class="property-features">
                            <div class="feature">
                                <i class="fas fa-hot-tub"></i> Jacuzzi
                            </div>
                            <div class="feature">
                                <i class="fas fa-wine-glass-alt"></i> Cave à vin
                            </div>
                            <div class="feature">
                                <i class="fas fa-concierge-bell"></i> Service
                                24h/24
                            </div>
                        </div>

                        <span class="price">€220<span>/nuit</span></span>
                        <button class="btn btn-primary">
                            Réserver maintenant
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="testimonials" id="temoignages">
            <h2 class="section-title animate-on-scroll">
                Ce que disent nos clients
            </h2>
            <p class="section-subtitle animate-on-scroll">
                Découvrez les témoignages de nos clients satisfaits
            </p>

            <div class="testimonial-cards">
                <div class="testimonial-card animate-on-scroll">
                    <p class="testimonial-text">
                        "RésidencePlus a révolutionné la gestion de mes
                        appartements. La plateforme est intuitive et m'a fait
                        gagner des heures de travail chaque semaine."
                    </p>
                    <div class="testimonial-author">
                        <img
                            src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80"
                            alt="Thomas"
                            class="author-img"
                        />
                        <div class="author-info">
                            <h4>Thomas Dubois</h4>
                            <p>Propriétaire de 6 appartements</p>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card animate-on-scroll">
                    <p class="testimonial-text">
                        "Depuis que j'utilise RésidencePlus pour gérer ma
                        chambre d'hôtel, j'ai augmenté mon taux d'occupation de
                        30%. Le système de réservation est parfait."
                    </p>
                    <div class="testimonial-author">
                        <img
                            src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80"
                            alt="Sophie"
                            class="author-img"
                        />
                        <div class="author-info">
                            <h4>Sophie Martin</h4>
                            <p>Gérante d'hôtel à Lyon</p>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card animate-on-scroll">
                    <p class="testimonial-text">
                        "Le suivi des paiements automatisé m'a évité bien des
                        soucis. Plus besoin de relancer les locataires, tout est
                        géré automatiquement."
                    </p>
                    <div class="testimonial-author">
                        <img
                            src="https://images.unsplash.com/photo-1552058544-f2b08422138a?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80"
                            alt="Pierre"
                            class="author-img"
                        />
                        <div class="author-info">
                            <h4>Pierre Lambert</h4>
                            <p>Investisseur immobilier</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="stats" id="services">
            <h2 class="section-title animate-on-scroll">Notre impact</h2>
            <p class="section-subtitle animate-on-scroll">
                Des chiffres qui parlent d'eux-mêmes
            </p>

            <div class="stats-grid">
                <div class="stat-card animate-on-scroll">
                    <i class="fas fa-building stat-icon"></i>
                    <div class="stat-value">1,250+</div>
                    <div class="stat-title">Propriétés gérées</div>
                </div>

                <div class="stat-card animate-on-scroll">
                    <i class="fas fa-users stat-icon"></i>
                    <div class="stat-value">850+</div>
                    <div class="stat-title">Clients satisfaits</div>
                </div>

                <div class="stat-card animate-on-scroll">
                    <i class="fas fa-euro-sign stat-icon"></i>
                    <div class="stat-value">98%</div>
                    <div class="stat-title">Paiements à temps</div>
                </div>

                <div class="stat-card animate-on-scroll">
                    <i class="fas fa-home stat-icon"></i>
                    <div class="stat-value">24/7</div>
                    <div class="stat-title">Support disponible</div>
                </div>
            </div>
        </section>

        <footer>
            <div class="footer-content">
                <div class="footer-col">
                    <h3>RésidencePlus</h3>
                    <p>
                        Votre solution complète pour la gestion de locations
                        immobilières. Simplifiez la gestion de vos appartements
                        et chambres en location.
                    </p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <div class="footer-col">
                    <h3>Liens rapides</h3>
                    <ul class="footer-links">
                        <li><a href="#">Accueil</a></li>
                        <li><a href="#">À propos de nous</a></li>
                        <li><a href="#">Nos services</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h3>Nos services</h3>
                    <ul class="footer-links">
                        <li><a href="#">Gestion locative</a></li>
                        <li><a href="#">Suivi des paiements</a></li>
                        <li><a href="#">Maintenance</a></li>
                        <li><a href="#">Gestion des contrats</a></li>
                        <li><a href="#">Rapports financiers</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h3>Contactez-nous</h3>
                    <ul class="footer-links">
                        <li>
                            <i class="fas fa-map-marker-alt"></i> 123 Rue de
                            l'Immobilier, Paris
                        </li>
                        <li><i class="fas fa-phone"></i> +33 1 23 45 67 89</li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            contact@residenceplus.fr
                        </li>
                        <li><i class="fas fa-clock"></i> Lun-Ven: 9h-18h</li>
                    </ul>
                </div>
            </div>

            <div class="copyright">
                &copy; 2023 RésidencePlus. Tous droits réservés.
            </div>
        </footer>
    </div>
</template>

<script>
export default {
    name: "Home",
    methods: {
        goToLogin() {
            this.$router.push("/login");
        },
        goToRegister() {
            this.$router.push("/register");
        },
    },
    mounted() {
        // Animation au défilement
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("visible");
                    }
                });
            },
            {
                threshold: 0.1,
            }
        );

        // Observer pour le header
        const header = document.getElementById("header");
        window.addEventListener("scroll", () => {
            if (window.scrollY > 50) {
                header.classList.add("scrolled");
            } else {
                header.classList.remove("scrolled");
            }
        });

        // Appliquer l'observer aux éléments
        document.querySelectorAll(".animate-on-scroll").forEach((el) => {
            observer.observe(el);
        });
    },
};
</script>

<style scoped>
:root {
    --primary: #3a86ff;
    --secondary: #ff006e;
    --accent: #8338ec;
    --dark: #212529;
    --light: #f8f9fa;
    --success: #06d6a0;
    --shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    --transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    --radius: 15px;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: "Montserrat", sans-serif;
    background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
    color: var(--dark);
    overflow-x: hidden;
    line-height: 1.6;
}

.home {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Header amélioré */
header {
    padding: 15px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 1000;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    border-radius: 0 0 var(--radius) var(--radius);
    transition: all 0.3s ease;
}

header.scrolled {
    padding: 10px 0;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.logo {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
}

.logo-icon {
    font-size: 2.2rem;
    color: var(--accent);
    transition: var(--transition);
}

.logo:hover .logo-icon {
    transform: rotate(-10deg);
}

.logo-text {
    font-family: "Playfair Display", serif;
    font-size: 1.8rem;
    font-weight: 700;
    background: linear-gradient(45deg, var(--primary), var(--accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: -0.5px;
}

nav ul {
    display: flex;
    list-style: none;
    gap: 30px;
}

nav a {
    text-decoration: none;
    color: var(--dark);
    font-weight: 500;
    transition: var(--transition);
    padding: 8px 12px;
    border-radius: 50px;
    position: relative;
}

nav a:hover {
    color: var(--primary);
}

nav a::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 2px;
    background: var(--primary);
    transition: var(--transition);
    transform: translateX(-50%);
}

nav a:hover::after {
    width: 70%;
}

.auth-buttons {
    display: flex;
    gap: 15px;
}

.btn {
    padding: 12px 24px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: var(--transition);
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    box-shadow: var(--shadow);
}

.btn-dark {
    background: var(--dark);
    color: black;
}

.btn-dark:hover {
    background: #343a40;
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.btn-info {
    background: linear-gradient(45deg, var(--primary), var(--accent));
    color: black;
}

.btn-info:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(58, 134, 255, 0.4);
}

/* Hero Section améliorée */
.hero {
    display: flex;
    align-items: center;
    min-height: 100vh;
    position: relative;
    overflow: hidden;
    padding: 60px 0;
    margin: 20px 0 40px;
    border-radius: var(--radius);
    background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
        url("https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80");
    background-size: cover;
    background-attachment: fixed;
    background-position: center;
    color: white;
    animation: fadeIn 1.2s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.hero-content {
    max-width: 700px;
    z-index: 10;
    padding: 0 40px;
    position: relative;
}

.hero h1 {
    font-family: "Playfair Display", serif;
    font-size: 3.8rem;
    font-weight: 700;
    line-height: 1.15;
    margin-bottom: 25px;
    color: white;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.hero h1 span {
    background: linear-gradient(45deg, #ffd700, #ffa500);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: block;
}

.hero p {
    font-size: 1.25rem;
    line-height: 1.8;
    margin-bottom: 35px;
    color: #f8f9fa;
    max-width: 600px;
}

.hero-buttons {
    display: flex;
    gap: 20px;
    margin-top: 40px;
}

.btn-primary {
    background: linear-gradient(30deg, var(--primary), var(--accent));
    color: black;
    padding: 15px 35px;
    font-size: 1.1rem;
}

.btn-primary:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 12px 25px rgba(58, 134, 255, 0.4);
}

.btn-secondary {
    background: transparent;
    color: white;
    border: 2px solid white;
    padding: 15px 35px;
    font-size: 1.1rem;
}

.btn-secondary:hover {
    background: white;
    color: var(--primary);
    transform: translateY(-5px);
}

/* Property Listings amélioré */
.properties {
    padding: 100px 0;
    text-align: center;
}

.section-title {
    font-family: "Playfair Display", serif;
    font-size: 2.8rem;
    margin-bottom: 15px;
    position: relative;
    display: inline-block;
    color: var(--dark);
}

.section-title::after {
    content: "";
    position: absolute;
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 5px;
    background: linear-gradient(45deg, var(--primary), var(--accent));
    border-radius: 5px;
}

.section-subtitle {
    font-size: 1.25rem;
    color: #6c757d;
    max-width: 700px;
    margin: 30px auto 60px;
}

.property-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 35px;
    margin-top: 30px;
}

.property-card {
    background: white;
    border-radius: var(--radius);
    overflow: hidden;
    transition: var(--transition);
    box-shadow: var(--shadow);
    position: relative;
    transform: translateY(0);
}

.property-card:hover {
    transform: translateY(-15px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.property-img {
    width: 100%;
    height: 280px;
    object-fit: cover;
    display: block;
    transition: transform 0.5s ease;
}

.property-card:hover .property-img {
    transform: scale(1.05);
}

.card-info {
    padding: 25px;
    text-align: left;
}

.card-info h3 {
    font-size: 1.6rem;
    margin-bottom: 15px;
    color: var(--dark);
}

.card-info p {
    color: #6c757d;
    margin-bottom: 20px;
    line-height: 1.7;
    min-height: 80px;
}

.property-features {
    display: flex;
    gap: 15px;
    margin: 20px 0;
    flex-wrap: wrap;
}

.feature {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.95rem;
    color: #495057;
    background: #f8f9fa;
    padding: 8px 15px;
    border-radius: 50px;
    transition: var(--transition);
}

.feature:hover {
    background: var(--primary);
    color: white;
}

.feature:hover i {
    color: white;
}

.feature i {
    color: var(--primary);
    font-size: 1rem;
    transition: var(--transition);
}

.price {
    font-size: 2rem;
    font-weight: 700;
    margin: 20px 0;
    color: var(--accent);
    display: block;
}

.price span {
    font-size: 1.1rem;
    color: #6c757d;
    font-weight: 400;
}

.property-badge {
    position: absolute;
    top: 20px;
    right: 20px;
    background: var(--secondary);
    color: white;
    padding: 6px 18px;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 600;
    box-shadow: 0 3px 10px rgba(255, 0, 110, 0.3);
    z-index: 2;
}

/* Testimonials amélioré */
.testimonials {
    padding: 100px 0;
    background: linear-gradient(135deg, #f0f4f8 0%, #e6eef7 100%);
    border-radius: var(--radius);
    margin: 60px 0;
}

.testimonial-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 40px;
    margin-top: 50px;
}

.testimonial-card {
    background: white;
    border-radius: var(--radius);
    padding: 35px;
    box-shadow: var(--shadow);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.testimonial-card::before {
    content: '"'; /* Correction de l'erreur ici */
    position: absolute;
    top: 20px;
    left: 20px;
    font-size: 6rem;
    font-family: Georgia, serif;
    color: var(--primary);
    opacity: 0.1;
    line-height: 1;
}

.testimonial-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.testimonial-text {
    font-style: italic;
    margin-bottom: 30px;
    color: #495057;
    line-height: 1.8;
    font-size: 1.1rem;
    position: relative;
    z-index: 2;
}

.testimonial-author {
    display: flex;
    align-items: center;
    gap: 20px;
}

.author-img {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--primary);
}

.author-info h4 {
    font-size: 1.3rem;
    margin-bottom: 5px;
    color: var(--dark);
}

.author-info p {
    color: var(--primary);
    font-size: 0.95rem;
    font-weight: 500;
}

/* Stats amélioré */
.stats {
    padding: 80px 0;
    text-align: center;
    background: linear-gradient(45deg, #3a86ff, #8338ec);
    border-radius: var(--radius);
    color: white;
    margin: 60px 0;
}

.stats .section-title {
    color: white;
}

.stats .section-subtitle {
    color: rgba(255, 255, 255, 0.85);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 30px;
    margin-top: 70px;
}

.stat-card {
    background: rgba(255, 255, 255, 0.15);
    border-radius: var(--radius);
    padding: 40px 20px;
    transition: var(--transition);
    backdrop-filter: blur(5px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.stat-card:hover {
    transform: translateY(-10px);
    background: rgba(255, 255, 255, 0.25);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
}

.stat-icon {
    font-size: 3rem;
    margin-bottom: 20px;
    color: white;
}

.stat-value {
    font-size: 2.8rem;
    font-weight: 700;
    margin-bottom: 10px;
    color: white;
    text-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.stat-title {
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 500;
}

/* Footer amélioré */
footer {
    background: var(--dark);
    color: white;
    padding: 80px 0 40px;
    border-radius: var(--radius) var(--radius) 0 0;
    margin-top: 100px;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 40px;
    margin-bottom: 50px;
}

.footer-col h3 {
    font-size: 1.6rem;
    margin-bottom: 25px;
    position: relative;
    padding-bottom: 15px;
}

.footer-col h3::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 4px;
    background: linear-gradient(45deg, var(--primary), var(--accent));
    border-radius: 2px;
}

.footer-col p {
    color: #adb5bd;
    line-height: 1.8;
    margin-bottom: 25px;
}

.footer-links {
    list-style: none;
}

.footer-links li {
    margin-bottom: 15px;
}

.footer-links a {
    text-decoration: none;
    color: #adb5bd;
    transition: var(--transition);
    display: block;
    padding: 5px 0;
}

.footer-links a:hover {
    color: var(--primary);
    padding-left: 10px;
}

.social-links {
    display: flex;
    gap: 15px;
    margin-top: 25px;
}

.social-links a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    color: white;
    transition: var(--transition);
    font-size: 1.2rem;
}

.social-links a:hover {
    background: var(--primary);
    transform: translateY(-5px);
}

.copyright {
    text-align: center;
    padding-top: 40px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    color: #adb5bd;
    font-size: 0.95rem;
}

/* Animation pour les sections */
.animate-on-scroll {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.8s ease, transform 0.8s ease;
}

.animate-on-scroll.visible {
    opacity: 1;
    transform: translateY(0);
}

/* Responsive */
@media (max-width: 992px) {
    header {
        flex-wrap: wrap;
        padding: 15px;
    }

    nav {
        order: 3;
        width: 100%;
        margin-top: 15px;
    }

    nav ul {
        justify-content: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .hero {
        min-height: auto;
        padding: 80px 0;
    }

    .hero h1 {
        font-size: 2.8rem;
    }

    .hero-content {
        padding: 0 20px;
    }
}

@media (max-width: 768px) {
    .hero h1 {
        font-size: 2.4rem;
    }

    .hero p {
        font-size: 1.1rem;
    }

    .hero-buttons {
        flex-direction: column;
        align-items: flex-start;
    }

    .section-title {
        font-size: 2.3rem;
    }
}

@media (max-width: 576px) {
    .auth-buttons {
        width: 100%;
        justify-content: center;
        margin-top: 15px;
    }

    .hero h1 {
        font-size: 2rem;
    }

    .property-cards {
        grid-template-columns: 1fr;
    }
}
</style>
