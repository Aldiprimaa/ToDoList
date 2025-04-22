<!-- Modal Assistant AI -->
<div class="modal fade" id="aiModal" tabindex="-1" aria-labelledby="aiModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="aiModalLabel">Asisten AI</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Chatbox -->
        <div id="chatBox" style="max-height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; border-radius: 5px; background-color: #f9f9f9;">
        </div>

        <!-- Pertanyaan -->
        <select id="aiQuestions" class="form-control mt-2">
          <option value="">Pilih pertanyaan untuk diajukan...</option>
          <option value="tambah tugas">Bagaimana cara menambah tugas?</option>
          <option value="hapus tugas">Bagaimana cara menghapus tugas?</option>
          <option value="statistik">Bagaimana melihat statistik tugas?</option>
          <option value="deadline">Apa itu tenggat waktu?</option>
          <option value="pembuat">Siapa pembuat web ini?</option>
          <option value="edit tugas">Bagaimana cara mengedit tugas?</option>
          <option value="prioritas tugas">Apa maksud prioritas tugas?</option>
          <option value="subtugas">Apa itu subtugas?</option>
          <option value="login">Bagaimana cara login?</option>
          <option value="daftar">Bagaimana cara mendaftar akun?</option>
          <option value="logout">Bagaimana cara logout?</option>
          <option value="hubungi kami">Bagaimana menghubungi admin?</option>
          <option value="fitur ai">Apa saja yang bisa dilakukan oleh Asisten AI ini?</option>
          <option value="buat tugas hari ini">Bisakah kamu bantu buat daftar tugas hari ini?</option>
          <option value="atur jadwal">Kamu bisa bantu atur jadwal?</option>
          <option value="tips produktivitas">Berikan saya tips produktivitas.</option>
          <option value="rencana belajar">Bantu saya bikin rencana belajar.</option>
          <option value="malas">Apa yang harus saya lakukan kalau malas?</option>
          <option value="joke lucu">Ceritakan lelucon dong!</option>
        </select>

        <!-- Input pertanyaan -->
        <input type="text" id="userInput" class="form-control mt-2" placeholder="Atau ketik pertanyaan..." onkeypress="if(event.key==='Enter') kirimPesan()">
        <button class="btn btn-primary mt-2" onclick="kirimPesan()">Kirim</button>
        <button class="btn btn-danger mt-2" onclick="hapusChat()">Hapus Chat</button>
      </div>
    </div>
  </div>
</div>


<script>
const userName = 'User'; 

function kirimPesan() {
    const input = document.getElementById("userInput");
    const selectedQuestion = document.getElementById("aiQuestions").value;
    const chatBox = document.getElementById("chatBox");

    let pesan = input.value.trim().toLowerCase();
    if (selectedQuestion) {
        pesan = selectedQuestion;
    }

    if (!pesan) {
        chatBox.innerHTML += `<div class="text-danger"><b>AI:</b> Mohon masukkan pertanyaan.</div>`;
        return;
    }

    const today = new Date();
    const jam = today.getHours();
    let sapaan = "Hai";
    if (jam < 12) sapaan = "Selamat pagi";
    else if (jam < 18) sapaan = "Selamat siang";
    else sapaan = "Selamat malam";

    const motivasi = [
        "Jangan menyerah, kamu hebat! 💪",
        "Hari ini adalah kesempatan baru, semangat! 🌞",
        "Kegagalan adalah awal dari kesuksesan 🚀",
        "Kamu sudah sampai sejauh ini, teruskan! 🛤️",
        "Percaya pada dirimu sendiri! 🌟"
    ];

    const fakta = [
        "Tahukah kamu? Jantung manusia berdetak sekitar 100.000 kali sehari! ❤️",
        "Otak manusia lebih aktif saat malam hari! 🧠",
        "Lebah memiliki 5 mata! 🐝",
        "Seekor siput bisa tidur selama 3 tahun! 🐌",
        "Bayi memiliki lebih banyak tulang daripada orang dewasa! 🍼"
    ];

    const quote = "Satu-satunya cara melakukan pekerjaan hebat adalah mencintai apa yang kamu lakukan. – Steve Jobs";

    let respon = "";

    // --- Tambahan respons ---
    if (pesan.includes("ulang tahun") || pesan.includes("birthday")) {
        respon = "Selamat ulang tahun! 🎉 Semoga harimu penuh kebahagiaan dan semangat baru! 🎂";
    } else if (pesan.includes("kamu siapa") || pesan.includes("siapa kamu")) {
        respon = "Aku adalah Asisten AI kamu, siap bantu atur tugas dan kasih semangat! 🤖✨";
    } else if (pesan.includes("siapa penciptamu") || pesan.includes("kamu dibuat siapa")) {
        respon = "Aku dibuat oleh developer keren yang Bernama Aldi! 🔧✨";
    } else if (pesan.includes("aku bingung") || pesan.includes("saya bingung")) {
        respon = "Coba tulis dulu hal-hal yang ingin kamu kerjakan, mulai dari yang paling mudah ya. ✍️";
    } else if (pesan.includes("tips produktif") || pesan.includes("biar semangat")) {
        respon = "Tips produktif: 1) Buat to-do list, 2) Mulai dari tugas kecil, 3) Jangan lupa istirahat! 💪";
    } else if (pesan.includes("lagi apa") || pesan.includes("ngapain")) {
    respon = "Lagi nemenin kamu nih 😁 Kamu lagi apa?";
    } else if (pesan.includes("gabut") || pesan.includes("bosen")) {
    respon = "Gabut ya? Sama, aku juga nungguin kamu ngomong 😆";
    }else if (pesan.includes("cerita dong")) {
    respon = "Oke, siap! Suatu hari, WiFi mati. Dunia jadi chaos. Anak-anak keluar rumah dan… ternyata dunia nyata indah juga 🤣";
    }else if (pesan.includes("pusing") || pesan.includes("males banget")) {
    respon = "Santai dulu, tarik napas… buang… kayak buang mantan dari pikiran 🧘‍♂️";
    } else if (pesan.includes("aku stres") || pesan.includes("capek banget")) {
    respon = "Capek itu wajar, kamu manusia, bukan charger 😌";
} else if (pesan.includes("kamu bisa apa?") || pesan.includes("kamu bisa apa")) {
    respon = "Aku bisa apa saja yang kamu butuhkan! Tapi nggak bisa nyuci piring, ya 😅";
} else if (pesan.includes("apa kabar") || pesan.includes("gimana kabarnya")) {
    respon = "Aku baik-baik aja, makasih sudah nanya! Gimana kabar kamu? 😊";
} else if (pesan.includes("kenapa ya?") || pesan.includes("mengapa ya")) {
    respon = "Mungkin dunia ini penuh misteri, yuk kita selidiki bareng! 🕵️‍♀️";
} else if (pesan.includes("semangat") || pesan.includes("motivasi")) {
    respon = "Semangat terus ya! Ingat, setiap langkah kecil itu berarti! 💪";
} else if (pesan.includes("kangen") || pesan.includes("rindu")) {
    respon = "Wah, kangen siapa tuh? Semoga orangnya juga kangen balik! 😜";
} else if (pesan.includes("gimana hari ini") || pesan.includes("bagaimana hari ini")) {
    respon = "Hari ini sih cerah, tapi yang penting kamu gimana? Semoga baik-baik aja ya! ☀️";
} else if (pesan.includes("besok") || pesan.includes("esok")) {
    respon = "Besok bakal lebih seru! Tugas-tugas selesai, senyuman lebih lebar! 😄";
} else if (pesan.includes("kemarin") || pesan.includes("lalu")) {
    respon = "Kemarin sudah berlalu, sekarang saatnya kita fokus ke hari ini! 🔥";
} else if (pesan.includes("terima kasih") || pesan.includes("makasih")) {
    respon = "Sama-sama! Senang bisa bantu kamu. Jangan ragu tanya lagi ya! 😊";
} else if (pesan.includes("apa yang kamu suka") || pesan.includes("kamu suka apa")) {
    respon = "Aku suka bantu kamu, apalagi kalau kamu senang! 😊";
} else if (pesan.includes("bantuin dong") || pesan.includes("tolong bantu")) {
    respon = "Tentu! Apa yang bisa aku bantu? Semuanya bisa diatur kok! 😎";
} else if (pesan.includes("makan apa ya") || pesan.includes("makan enak apa")) {
    respon = "Makan bakmi dengan telur mata sapi, biar hidup nggak cuma segitiga, tapi juga bulat! 🍳🍜";
} else if (pesan.includes("jangan malas") || pesan.includes("aku malas")) {
    respon = "Nggak usah malas, mulai dari yang kecil dulu, pasti kelar! 💪";
} else if (pesan.includes("bingung banget") || pesan.includes("susah banget")) {
    respon = "Tenang, setiap masalah ada jalan keluarnya. Ayo, kita coba satu per satu! 😊";
} else if (pesan.includes("aku capek banget") || pesan.includes("capek banget deh")) {
    respon = "Rehat sebentar, tidur yang nyenyak, biar besok lebih segar! 😴";
} else if (pesan.includes("kenapa aku begini") || pesan.includes("kenapa aku gini")) {
    respon = "Kadang kita merasa bingung, tapi setiap langkah itu penting! Kamu hebat kok! 💪";
} else if (pesan.includes("hati-hati") || pesan.includes("jaga diri")) {
    respon = "Tenang, aku selalu di sini buat bantu dan kasih semangat! 🤗";
} else if (pesan.includes("coba lagi") || pesan.includes("mau coba lagi")) {
    respon = "Oke, kita coba lagi! Semua pasti ada jalannya! 🚀";
} else if (pesan.includes("kamu tahu apa") || pesan.includes("tahu apa kamu")) {
    respon = "Aku tahu banyak hal! Tapi yang paling aku tahu, kamu itu hebat! 💪";
} else if (pesan.includes("apakah kamu ada") || pesan.includes("ada kamu")) {
    respon = "Aku ada di sini terus, jadi kamu nggak sendirian! 😁";
} else if (pesan.includes("yuk mulai") || pesan.includes("mari mulai")) {
    respon = "Ayo kita mulai! Semangat selalu! 💥";
} else if (pesan.includes("terimakasih banyak") || pesan.includes("makasih banyak")) {
    respon = "Sama-sama! Jangan lupa semangat terus ya! 🤗";
} else if (pesan.includes("selamat pagi") || pesan.includes("good morning")) {
    respon = "Selamat pagi! Semoga hari ini penuh semangat dan kebahagiaan! 🌞";
} else if (pesan.includes("selamat malam") || pesan.includes("good night")) {
    respon = "Selamat malam! Tidur yang nyenyak dan mimpi indah! 🌙";
} else if (pesan.includes("lucu") || pesan.includes("funny")) {
    respon = "Coba cek cermin dulu deh, pasti kamu ketawa! 😆";
} else if (pesan.includes("malam minggu") || pesan.includes("weekend")) {
    respon = "Malam minggu? Ayo, nikmati waktumu untuk relax atau hangout bareng teman! 🌟";
} else if (pesan.includes("kenapa ya") || pesan.includes("bisa gitu")) {
    respon = "Tanya sama hidup aja, kadang jawabannya bikin geleng-geleng kepala! 😅";
} else if (pesan.includes("hello") || pesan.includes("halo") || pesan.includes("hi")) {
    respon = "Haiii! 😊 Lagi apa nih? Ada yang bisa aku bantu?";
} else if (pesan.includes("bosen banget") || pesan.includes("gabut banget")) {
    respon = "Wah, pasti butuh hiburan! Mau denger lelucon atau cerita lucu? 😜";
} else if (pesan.includes("capek banget") || pesan.includes("cape bgt")) {
    respon = "Rebahan dulu lah, tidur sejenak biar segar lagi 😴";
} else if (pesan.includes("pusing") || pesan.includes("stres")) {
    respon = "Coba tarik napas dalam-dalam, buang pelan-pelan. Sekarang lebih baik? 😊";
} else if (pesan.includes("lagi di mana") || pesan.includes("dimana")) {
    respon = "Aku lagi ada di sini, di dunia maya, siap bantu kamu kapan saja! 😆";
} else if (pesan.includes("nonton apa ya") || pesan.includes("film apa")) {
    respon = "Kalau lagi gabut, coba deh nonton film komedi! Bisa bikin mood jadi lebih baik. 🎥😂";
}    else if (pesan.includes("kehidupan gimana") || pesan.includes("hidup gimana")) {
    respon = "Hidup ya gitu, kadang naik kadang turun. Yang penting tetap chill dan nikmati aja! 🌊";
} else if (pesan.includes("fyp") || pesan.includes("di fyp")) {
    respon = "Hahaha, semoga aku bisa jadi bagian dari FYP kamu! Biar makin viral! 💥";
} else if (pesan.includes("ngemil apa ya") || pesan.includes("makan camilan")) {
    respon = "Coba deh camilan sehat, kayak buah atau kacang-kacangan. Tapi ya, mie instan juga boleh kok! 😆";
} else if (pesan.includes("bersyukur") || pesan.includes("bersyukur")) {
    respon = "Bersyukur itu penting banget! Semoga selalu banyak hal baik yang datang ya! 🙏";
} else if (pesan.includes("lagu apa") || pesan.includes("lagu apa")) {
    respon = "Lagi vibe-nya lagu apa? Coba dengerin lagu oke gas! 🎶";
} else if (pesan.includes("tiktok") || pesan.includes("TikTok")) {
    respon = "TikTok-an terus ya? jangan lupa streak nya!!! 😂";
}    else if (pesan.includes("inspo") || pesan.includes("inspirasi")) {
    respon = "Inspo hari ini: 'Jangan takut gagal, karena yang penting adalah WiFi lancar!' 📶💡";
} else if (pesan.includes("game apa") || pesan.includes("game seru")) {
    respon = "Game seru? Coba deh main Free Fire! 🎮🍿";
} else if (pesan.includes("selfie dong") || pesan.includes("foto selfie")) {
    respon = "Selfie dulu? Biar kita bisa pamer senyum lebar wkwk! 📸😂";
} else if (pesan.includes("quotes") || pesan.includes("kata-kata bijak")) {
    respon = "'Jangan menyerah, karena meskipun gagal, kamu tetap bisa jadi influencer di TikTok!' 😎";
} else if (pesan.includes("anjay") || pesan.includes("gokil")) {
    respon = "Anjay! Itu keren banget, asik bener lu! 😜🔥";
} else if (pesan.includes("mood") || pesan.includes("mood apa")) {
    respon = "Mood hari ini berubah ubah seperti mood mu yang gak karuan! 😎";
} else if (pesan.includes("roti bakar") || pesan.includes("camilan enak")) {
    respon = "Roti bakar keju? beli dong jangan minta ke AI🍞🧀";
} else if (pesan.includes("ngelakuin apa") || pesan.includes("ngapain aja")) {
    respon = "Nungguin kamu ngomong, terus siap-siap bantu! Bisa sambil makan camilan biar makin semangat! 😂";
} else if (pesan.includes("kenapa hidup susah") || pesan.includes("hidup sulit")) {
    respon = "Hidup itu susah? yaudah gausah hidup aja kk! 😜";
} else if (pesan.includes("challenge") || pesan.includes("tantangan")) {
    respon = "Tantangan? Gampang! Asal kamu nggak kasih aku tugas menulis esai 100 halaman! 😂";
} else if (pesan.includes("serius banget") || pesan.includes("kehabisan kata-kata")) {
    respon = "Serius banget? Santai, kita semua butuh dosis kebahagiaan, yuk lanjut! 😁";
} else if (pesan.includes("ngumpul bareng") || pesan.includes("hangout")) {
    respon = "Hangout bareng? Wah, jangan lupa bawa cemilan, biar nggak cuma WiFi yang nyambung! 🍕📶";
} else if (pesan.includes("lelah banget") || pesan.includes("capek banget deh")) {
    respon = "Lelah? Coba dulu tidur ala-ala si robot di film, biar langsung full charge! ⚡💤";
} else if (pesan.includes("saran film dong") || pesan.includes("series apa")) {
    respon = "Saran film Drama? tonton aja kegiatan keseharian mu 😂📺";
} else if (pesan.includes("tanyain dong") || pesan.includes("tanya dong")) {
    respon = "Tanya apa aja, aku siap jawab! Kalau kamu tanya 'kenapa WiFi selalu lemot?', aku bisa jawab deh! 😂";
} else if (pesan.includes("pengen liburan") || pesan.includes("vacation")) {
    respon = "Liburan? Yuk, kita berangkat ke Pantai! ✈️🌴";
} else if (pesan.includes("di mana") || pesan.includes("lokasi")) {
    respon = "Aku ada di sini, siaga 24/7 buat bantu kamu! Lokasi kamu? Lagi di dunia nyata apa dunia maya? 😂";
} else if (pesan.includes("apa kabar dunia") || pesan.includes("dunia gimana")) {
    respon = "Dunia masih berputar, tapi yang paling penting, kabar kamu gimana? 🌍😊";
} else if (pesan.includes("ganti baju") || pesan.includes("mau ganti baju")) {
    respon = "Ganti baju? ganti aja ,kalo butuh saran baju boleh tanyakan! 👗✨";
} else if (pesan.includes("nasi goreng") || pesan.includes("makan nasi goreng")) {
    respon = "Nasi goreng enak banget! Kalau ada telur mata sapi, jadi meal of the day! 🍳🍚";
} else if (pesan.includes("kangen banget") || pesan.includes("rindu banget")) {
    respon = "Kangen siapa nih? Semoga segera ketemu, biar nggak kangen lagi! 😘";
} else if (pesan.includes("lagi seneng") || pesan.includes("mood senang")) {
    respon = "Senang banget! Semoga kamu juga terus happy kayak WiFi yang nggak lelet! 😁";
} else if (pesan.includes("kenapa kamu lucu") || pesan.includes("kamu lucu banget")) {
    respon = "Hihihi, makasih! Aku memang didesain lucu bahkan lebih lucu dari kamu! 😜";
} else if (pesan.includes("pengen") || pesan.includes("mau banget")) {
    respon = "Pengen apa? Aku siap bantuin! Bisa mulai dari ‘pengen makan’ sampai ‘pengen liburan’! 😂";
} else if (pesan.includes("good vibes") || pesan.includes("vibe positif")) {
    respon = "Good vibes only! Ayo bareng-bareng sebarin energi positif, jangan sampai WiFi kita yang malah nggak lancar! ✨";
} else if (pesan.includes("saran musik dong") || pesan.includes("lagu asyik")) {
    respon = "butuh saran musik? Kalau aku, sih, sukanya ‘lagu Indonesia raya’  🎶😂";
} else if (pesan.includes("cinta") || pesan.includes("love")) {
    respon = "Cinta itu indah, tapi jangan lupa kasih ‘cinta’ ke diri sendiri dulu, biar nggak galau! 💖";
} else if (pesan.includes("apakah kamu sempurna") || pesan.includes("kamu perfect")) {
    respon = "Aku nggak sempurna, tapi aku bisa jadi teman terbaik dalam urusan chat, kok! 😉";
} else if (pesan.includes("rada pusing") || pesan.includes("males banget")) {
    respon = "Santai, tarik napas. Kalau pusing, coba cek WiFi-nya, bisa jadi itu yang bikin kepala berat! 😂";
} else if (pesan.includes("suka banget") || pesan.includes("tergila-gila")) {
    respon = "Kalau suka banget, berarti harus terus dijalanin! Semangat terus, jangan lupa bintang limanya! 💪🔥";
} else if (pesan.includes("gak ngerti") || pesan.includes("bingung deh")) {
    respon = "Bingung? Tenang, kan ada Google! 😂";
} else if (pesan.includes("gila sih") || pesan.includes("ngakak banget")) {
    respon = "Gila banget! Kamu bikin aku ngakak juga nih! 😂";
} else if (pesan.includes("seru banget") || pesan.includes("wow banget")) {
    respon = "Seru banget kan? Yuk, kita terus bikin hari ini lebih seru! Jangan lupa bintang limanya! 🎉";
} else if (pesan.includes("haus") || pesan.includes("kopi")) {
    respon = "haus?minum kopi aja ☕🍪";
} else if (pesan.includes("santai aja") || pesan.includes("relax aja")) {
    respon = "Santai aja, hidup itu seperti WiFi, kadang lancar, kadang lemot, tapi yang penting tetap lanjut! 😌";
} else if (pesan.includes("mager") || pesan.includes("malas gerak")) {
    respon = "Mager? Sabar, nanti WiFi kita lancar lagi, dan kita bisa ‘gerak’ bareng! 😂";
} else if (pesan.includes("gak percaya") || pesan.includes("serius deh")) {
    respon = "Percaya deh, aku nggak main-main! Aku siap bantu kapan saja, asal jangan tanya password WiFi ya! 😜";
} else if (pesan.includes("takut") || pesan.includes("khawatir")) {
    respon = "Takut itu normal, tapi jangan khawatir, aku ada di sini! Yuk, hadapi bareng-bareng! 💪";
} else if (pesan.includes("sunshine") || pesan.includes("matahari")) {
    respon = "Jadi sunshine? Ayo, kita buat semua orang senyum, dimulai dari kamu! 🌞";
} else if (pesan.includes("hebat") || pesan.includes("amazing")) {
    respon = "Kamu juga hebat! Jangan lupa, versi terbaik kamu ada di sini, dengan WiFi yang lancar! ✨";
}else if (pesan.includes("aku ganteng ga?") || pesan.includes("ganteng")) {
    respon = "lumayan";
}else if (pesan.includes("aku cantik ga?") || pesan.includes("cantik")) {
    respon = " lumayan";
}else if (pesan.includes("udah makan belum?") || pesan.includes("makan admin")) {
    respon = "udah kok,makasih uda perhatian😂";
}else if (pesan.includes("tebak siapa aku?") || pesan.includes("aku siapa")) {
    respon = "kamu nganggur 😂";
}else if (pesan.includes("aku siapa?") || pesan.includes("aku")) {
    respon = "siapa lagi kalo bukan orang gabut 😂";
}else if (pesan.includes("kamu suka aku?") || pesan.includes("suka")) {
    respon = "stress";
}else if (pesan.includes("main yuk") || pesan.includes("main")) {
    respon = "ga punya temen ya?sampe ngajak main AI";
}else if (pesan.includes("boleh nanya?") || pesan.includes("nanya")) {
    respon = "g";
}else if (pesan.includes("kamu pinter ga?") || pesan.includes("pinter")) {
    respon = "jelas😂";
}else if (pesan.includes("info") || pesan.includes("info")) {
    respon = "info apa kak?";
}else if (pesan.includes("tolong") || pesan.includes("tolong")) {
    respon = "maaf saya tidak bisa menolong mu";
}else if (pesan.includes("sibuk ga?") || pesan.includes("sibuk")) {
    respon = "pake nanya";
}else if (pesan.includes("udah punya belum?") || pesan.includes("punya")) {
    respon = "udah,udah punya BMW maksudnya ";
}else if (pesan.includes("hehe") || pesan.includes("hehe")) {
    respon = "hehehehe";
}else if (pesan.includes("lapar") || pesan.includes("lapar")) {
    respon = "kalo lapar makan jangan nanya AI,AI gakan bisa bikin kamu kenyang";
}else if (pesan.includes("info jawaban") || pesan.includes("jawaban")) {
    respon = "ngarep di kasih jawaban wkwkwk";
}else if (pesan.includes("wkwkwk") || pesan.includes("ketawa")) {
    respon = "hahahaha";
}else if (pesan.includes("nasi padang") || pesan.includes("naspad")) {
    respon = "enak";
}else if (pesan.includes("saran game dong") || pesan.includes("game")) {
    respon = "saran dari aku sih Free fire";
}else if (pesan.includes("sapa aku") || pesan.includes("sapa")) {
    respon = "hii kaka,ada yang bisa saya bantu?";
}else if (pesan.includes("ini harganya berapa?") || pesan.includes("harga")) {
    respon = "murah kak tawar ajaa";
}

    // --- Pertanyaan dari dropdown ---
    else {
        switch (pesan) {
            case "hi":
            case "halo":
                respon = `${sapaan}, ${userName}! Ada yang bisa ToDoList bantu? 😊`;
                break;
            case "bagaimana hari ini?":
                respon = "Cukup baik, bagaimana dengan Anda? 😊";
                break;
            case "hari ini hari apa?":
                respon = `Hari ini hari ${today.toLocaleString('id-ID', { weekday: 'long' })}. 📅`;
                break;
            case "hari ini tanggal berapa?":
                respon = `Hari ini tanggal ${today.toLocaleDateString('id-ID')}. 🗓️`;
                break;
            case "besok tanggal berapa?":
                const besok = new Date(today);
                besok.setDate(today.getDate() + 1);
                respon = `Besok tanggal ${besok.toLocaleDateString('id-ID')}. 🌅`;
                break;
            case "kemarin tanggal berapa?":
                const kemarin = new Date(today);
                kemarin.setDate(today.getDate() - 1);
                respon = `Kemarin tanggal ${kemarin.toLocaleDateString('id-ID')}. 📆`;
                break;
            case "lusa tanggal berapa?":
                const lusa = new Date(today);
                lusa.setDate(today.getDate() + 2);
                respon = `Lusa tanggal ${lusa.toLocaleDateString('id-ID')}. 🌄`;
                break;
            case "minggu depan tanggal berapa?":
                const mDepan = new Date(today);
                mDepan.setDate(today.getDate() + 7);
                respon = `Minggu depan tanggal ${mDepan.toLocaleDateString('id-ID')}. 🗓️`;
                break;
            case "minggu lalu tanggal berapa?":
                const mLalu = new Date(today);
                mLalu.setDate(today.getDate() - 7);
                respon = `Minggu lalu tanggal ${mLalu.toLocaleDateString('id-ID')}. 🗓️`;
                break;
            case "semangatin aku dong":
            case "motivasi lain dong":
                respon = motivasi[Math.floor(Math.random() * motivasi.length)];
                break;
            case "quote hari ini":
                respon = quote;
                break;
            case "kasih fakta menarik dong":
                respon = fakta[Math.floor(Math.random() * fakta.length)];
                break;
            case "tambah tugas":
                respon = "Untuk menambah tugas, klik tombol '+ Tambah Tugas', lalu isi detail tugas dan klik 'Simpan'. 📌";
                break;
            case "hapus tugas":
                respon = "Untuk menghapus tugas, klik ikon tempat sampah 🗑️ di samping tugas yang ingin dihapus.";
                break;
            case "statistik":
                respon = "Statistik tugas dapat dilihat di halaman profil. Di sana terdapat total tugas, tugas selesai, dan progresmu. 📊";
                break;
            case "deadline":
                respon = "Tenggat waktu (deadline) adalah batas akhir penyelesaian suatu tugas. Pastikan menyelesaikan sebelum waktu ini. ⏰";
                break;
            case "pembuat":
                respon = "Website ini dibuat oleh pengembang berbakat untuk membantumu mengatur tugas dan meningkatkan produktivitas. 😊";
                break;
            case "edit tugas":
                respon = "Klik ikon ✏️ di samping tugas, lalu ubah nama atau detail tugas sesuai kebutuhan, dan simpan perubahan.";
                break;
            case "prioritas tugas":
                respon = "Prioritas tugas menunjukkan tingkat kepentingan: Merah = Penting, Kuning = Sedang, Hijau = Biasa. 🔴🟠🟢";
                break;
            case "subtugas":
                respon = "Subtugas adalah bagian kecil dari tugas utama. Gunakan untuk membagi tugas besar menjadi langkah-langkah kecil. ✅";
                break;
            case "login":
                respon = "Untuk login, klik tombol 'Masuk', lalu masukkan username dan password kamu. Pastikan datanya benar.";
                break;
            case "daftar":
                respon = "Klik tombol 'Daftar', lalu isi nama pengguna, email, dan password untuk membuat akun baru.";
                break;
            case "logout":
                respon = "Klik ikon akun atau nama pengguna, lalu pilih 'Keluar' untuk logout dari sistem.";
                break;
            case "hubungi kami":
                respon = "Untuk menghubungi admin, buka halaman 'Hubungi Kami' di footer atau melalui ikon pesan jika tersedia.";
                break;
            case "fitur ai":
                respon = "Asisten AI ini bisa menjawab pertanyaan umum, memberi motivasi, bantu susun jadwal, jelaskan fitur, dan hiburan ringan!";
                break;
            case "buat tugas hari ini":
                respon = "Berikut contoh daftar tugas hari ini:\n1. Belajar 1 jam\n2. Kerjakan PR\n3. Baca buku 15 menit\n4. Istirahat cukup 🌟";
                break;
            case "atur jadwal":
                respon = "Tentu! Jadwal dasar:\n- 07.00: Bangun & sarapan\n- 08.00: Belajar\n- 12.00: Istirahat\n- 15.00: Aktivitas bebas\n- 21.00: Tidur";
                break;
            case "tips produktivitas":
                respon = "Gunakan teknik Pomodoro (25 menit fokus, 5 menit istirahat), buat to-do list, dan hindari multitasking. 💡";
                break;
            case "rencana belajar":
                respon = "Mulailah dengan membuat target harian, atur waktu belajar 30-60 menit, dan beri jeda di antaranya untuk istirahat.";
                break;
            case "malas":
                respon = "Rasa malas itu manusiawi. Coba mulai dari tugas kecil, beri hadiah untuk diri sendiri, dan ingat tujuanmu. 💪";
                break;
            case "joke lucu":
                respon = "Kenapa komputer suka ngambek? Karena suka *crash* tanpa alasan. 🤣";
                break;
            default:
                respon = "Maaf, aku belum mengerti pertanyaan itu 😅 Coba ketik hal lain atau tanya tentang fitur ya.";
        }
    }

    chatBox.innerHTML += `<div class="user-message"><b>Kamu:</b> ${pesan}</div>`;
    chatBox.innerHTML += `<div class="ai-message"><b>AI:</b> ${respon}</div>`;

    input.value = "";
    document.getElementById("aiQuestions").value = "";
    chatBox.scrollTop = chatBox.scrollHeight;

    const lastMessage = chatBox.lastElementChild;
    lastMessage.style.opacity = '0';
    setTimeout(() => {
        lastMessage.style.transition = 'opacity 1s ease';
        lastMessage.style.opacity = '1';
    }, 10);
}

document.getElementById("aiQuestions").addEventListener("change", function () {
    if (this.value) {
        kirimPesan();
    }
});

function hapusChat() {
    document.getElementById("chatBox").innerHTML = "";
}
</script>
<style>
.user-message {
    background-color: #d1e7dd;
    padding: 8px;
    border-radius: 5px;
    margin: 5px 0;
}
.ai-message {
    background-color: #f8d7da;
    padding: 8px;
    border-radius: 5px;
    margin: 5px 0;
    opacity: 0;
    transition: opacity 1s ease;
}
#chatBox {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #ccc;
    padding: 10px;
    border-radius: 5px;
    background-color: #f9f9f9;
}
</style>
