import http from "k6/http";
import { check } from "k6";

export let options = {
    vus: 1,          
    duration: "20s",
};

export default function () {

    const loginUrl = "http://localhost/TRPWIFix/userlogin.php";

    // Payload login user
    const payload = {
        username: "Kresna",        
        password: "Kresna1234"      
    };

    const params = {
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        }
    };

    // POST ke login user
    let res = http.post(loginUrl, payload, params);

    check(res, {
        "Status 200": (r) => r.status === 200,
        "Tidak ada error PHP": (r) =>
            !r.body.includes("Warning") &&
            !r.body.includes("Fatal") &&
            !r.body.includes("Notice"),
        "Login berhasil (ada sesi)": (r) =>
            r.cookies.PHPSESSID !== undefined,
        "Redirect ke dashboard user": (r) =>
            r.headers.Location && r.headers.Location.includes("userdash.php"),
    });
}
