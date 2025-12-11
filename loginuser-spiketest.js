import http from "k6/http";
import { check } from "k6";

export let options = {
    stages: [
        { duration: "5s", target: 10 },    
        { duration: "1s", target: 200 },  
        { duration: "20s", target: 200 },   
        { duration: "5s", target: 0 },      
    ],
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
        "status OK": r => r.status === 200,
        "session dibuat": r => r.cookies.PHPSESSID !== undefined
    });
}
