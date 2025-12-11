import http from "k6/http";
import { check, sleep } from "k6";

export const options = {
  vus: 20,             // 20 pengguna aktif
  duration: "30s",     // durasi test
};

export default function () {
  const url = "http://localhost/TRPWIFix/databuku.php";

  // Kalau halaman butuh login admin/user:
  const params = {
    headers: {
      "Cookie": "PHPSESSID=ISI_SESSION_YANG_VALID" 
    }
  };

  const res = http.get(url, params);

  check(res, {
    "status code 200": (r) => r.status === 200,
    "halaman buku tampil": (r) => r.body.includes("Buku") || r.body.includes("Daftar") || r.body.length > 100,
    "tidak ada error PHP": (r) =>
      !r.body.includes("Warning") &&
      !r.body.includes("Fatal") &&
      !r.body.includes("Notice"),
  });

  sleep(1);
}
