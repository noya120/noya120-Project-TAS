import http from "k6/http";
import { sleep } from "k6";

export let options = {
    stages: [
        { duration: "30s", target: 20 },   // mulai naik 20 user
        { duration: "30s", target: 50 },   // naik 50
        { duration: "30s", target: 100 },  // push ke 100 (mencari batas)
        { duration: "30s", target: 0 },    // turun
    ],
};

export default function () {
    http.get("http://localhost/TRPWIFix/index.php");
    sleep(1);
}
