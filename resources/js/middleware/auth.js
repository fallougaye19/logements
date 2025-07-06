export default function auth(to, from, next) {
    const token = localStorage.getItem("token");

    if (!token) {
        next({ path: "/login" });
    } else {
        next();
    }
}
