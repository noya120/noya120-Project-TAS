import http from "k6/http";
import { check } from "k6";

export let options = {
    vus: 20,              // 20 user simultan = beban normal
    duration: "30s",      // durasi 30 detik
};

export default function () {

    const loginUrl = "http://localhost/TRPWIFix/userlogin.php";

    const payload = {
        username: "Kresna",
        password: "Kresna1234"
    };

    const params = {
        headers: { "Content-Type": "application/x-www-form-urlencoded" }
    };

    let res = http.post(loginUrl, payload, params);

    check(res, {
        "status 200": r => r.status === 200,
        "session dibuat": r => r.cookies.PHPSESSID !== undefined,
        "redirect ke dashboard": r =>
            r.headers.Location && r.headers.Location.includes("userdash.php")
    });
}
