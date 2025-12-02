import axios from "axios";
import Swal from "sweetalert2";

window.axios = axios;
window.Swal = Swal;
let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
} else {
    console.error("CSRF token not found");
}
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
