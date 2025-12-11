import http from "k6/http";
import { sleep } from "k6";

export let options = {
    stages: [
        { duration: "10s", target: 10 },    // normal
        { duration: "1s", target: 200 },    // spike tiba-tiba
        { duration: "10s", target: 10 },    // kembali turun
    ],
};

export default function () {
    http.get("http://localhost/TRPWIFix/index.php");
    sleep(1);
}
