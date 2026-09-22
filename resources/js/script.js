// Detail Barang Foto Selector
document.querySelectorAll("[data-photo]").forEach((thumbnail) => {
    thumbnail.addEventListener("click", () => {
        const detailMainImage = document.getElementById("detail-main-image");

        if (!detailMainImage || !thumbnail.dataset.photo) {
            return;
        }

        detailMainImage.src = thumbnail.dataset.photo;
        detailMainImage.alt = thumbnail.dataset.photoAlt ?? detailMainImage.alt;
        document
            .querySelectorAll("[data-photo]")
            .forEach((item) => item.classList.remove("active"));
        thumbnail.classList.add("active");
    });
});

// Stok Input Form Number Stepper
function adjustQty(button, delta) {
    const input = button
        .closest(".input-group")
        ?.querySelector('input[type="number"]');

    if (!input) {
        return;
    }

    const currentValue = parseInt(input.value, 10);
    const minValue = parseInt(input.min, 10) || 0;
    const stepValue = parseInt(input.step, 10) || 1;
    const nextValue = Number.isNaN(currentValue)
        ? minValue
        : currentValue + delta * stepValue;

    input.value = Math.max(minValue, nextValue);
}

document.addEventListener("click", (event) => {
    const button = event.target.closest(".qty-adjust-btn");

    if (!button) {
        return;
    }

    const delta = Number.parseInt(button.dataset.qtyDelta ?? "1", 10);
    adjustQty(button, delta);
});

// Live Search (debounced) — swaps table rows from an AJAX fragment
document.querySelectorAll(".live-search").forEach((input) => {
    const debounce = Number.parseInt(input.dataset.debounce ?? "800", 10);
    const target = document.getElementById(input.dataset.target);
    const baseUrl = new URL(
        input.closest("form")?.action || window.location.href,
    );
    let timer = null;
    let seq = 0;

    const run = () => {
        baseUrl.searchParams.set("q", input.value.trim());
        const current = ++seq;
        clearTimeout(timer);

        fetch(baseUrl, { headers: { "X-Requested-With": "XMLHttpRequest" } })
            .then((res) => res.text())
            .then((html) => {
                if (current !== seq) {
                    return; // stale response — a newer query superseded this one
                }

                if (target) {
                    target.innerHTML = html;
                }
            });
    };

    input.addEventListener("input", () => {
        clearTimeout(timer);
        timer = setTimeout(run, debounce);
    });
});

// Ukuran Price Updater
const ukuranSelect = document.getElementById("id_ukuran");
const priceDisplay = document.getElementById("price-display");

if (ukuranSelect && priceDisplay) {
    const prices = {};
    ukuranSelect.querySelectorAll("option[data-harga]").forEach((opt) => {
        prices[opt.value] = opt.dataset.harga;
    });
    const defaultPrice = priceDisplay.textContent;
    const formatPrice = (n) => "Rp " + Number(n).toLocaleString("id-ID");

    ukuranSelect.addEventListener("change", () => {
        const selected = ukuranSelect.value;
        priceDisplay.textContent =
            prices[selected] !== undefined
                ? formatPrice(prices[selected])
                : defaultPrice;
    });
}

// Keranjang max-stok check (styled, replaces native 'max' bubble)
document
    .querySelectorAll("form[novalidate] input[name='jumlah_barang']")
    .forEach((input) => {
        const error = input
            .closest("div")
            ?.querySelector("[data-jumlah-error]");
        const form = input.closest("form");

        const validate = () => {
            const val = Number(input.value);
            const max = Number(input.max);
            const tooHigh = max && val > max;
            const tooLow = val < Number(input.min || 1);
            let msg = "";
            if (tooHigh) msg = "Maksimal stok " + max + " unit.";
            else if (tooLow) msg = "Minimal 1 unit.";
            if (error) {
                error.textContent = msg;
                error.classList.toggle("d-none", msg === "");
            }
            return tooHigh || tooLow;
        };

        input.addEventListener("input", validate);
        form.addEventListener("submit", (e) => {
            if (validate()) e.preventDefault();
        });
    });

// Keranjang checkout: require at least one checked item
(() => {
    const btn = document.getElementById("checkout-selected");
    if (!btn) return;
    const form = btn.closest("form");
    const err = document.getElementById("checkout-error");
    form.addEventListener("submit", (e) => {
        const checked = document.querySelectorAll(".keranjang-check:checked");
        if (!checked.length) {
            e.preventDefault();
            err.textContent = "Pilih minimal satu barang untuk checkout.";
            err.classList.remove("d-none");
            return;
        }
        checked.forEach((c) => {
            const hidden = document.createElement("input");
            hidden.type = "hidden";
            hidden.name = "id_keranjang[]";
            hidden.value = c.value;
            form.appendChild(hidden);
        });
    });
    document
        .querySelectorAll(".keranjang-check")
        .forEach((c) =>
            c.addEventListener("change", () => err.classList.add("d-none")),
        );
})();

// Toast countdown progress (shrinks over the toast delay)
document.querySelectorAll(".toast[data-bs-delay]").forEach((toast) => {
    if (!window.bootstrap?.Toast) {
        return;
    }

    const bar = toast.querySelector(".toast-progress");
    const delay = Number(toast.dataset.bsDelay) || 5000;

    if (bar) {
        requestAnimationFrame(() => {
            bar.style.transitionDuration = delay + "ms";
            bar.style.width = "0%";
        });
    }

    setTimeout(() => {
        bootstrap.Toast.getOrCreateInstance(toast).hide();
    }, delay);
});

// Harga field formatting (integer, id-ID thousand dots)
const toIntegerDigits = (s) => {
    s = (s || "").trim();
    const m = s.match(/^([\d.,]+?)[.,](\d{1,2})$/);
    return m ? m[1].replace(/[.,]/g, "") : s.replace(/[.,]/g, "");
};
const moneyFormat = (raw) => {
    const digits = toIntegerDigits(raw);
    return digits === "" ? "" : Number(digits).toLocaleString("id-ID");
};

document
    .querySelectorAll("input[name='harga'], input[name='harga_ukuran'], input[name='min'], input[name='max']")
    .forEach((el) => {
        el.value = moneyFormat(el.value);

        el.addEventListener("input", () => {
            const formatted = moneyFormat(el.value);
            if (el.value !== formatted) {
                el.value = formatted;
                el.scrollLeft = el.scrollWidth;
            }
        });

        el.closest("form").addEventListener("submit", () => {
            el.value = toIntegerDigits(el.value);
        });
    });

// Harga minimal 1 (client-side guard for barang main price)
document.querySelectorAll("input[name='harga']").forEach((el) => {
    const form = el.closest("form");
    let msg = null;
    const clearMsg = () => {
        if (msg) {
            msg.remove();
            msg = null;
        }
    };
    el.addEventListener("input", clearMsg);
    form.addEventListener("submit", (e) => {
        const digits = toIntegerDigits(el.value);
        if (digits === "" || Number(digits) === 0) {
            e.preventDefault();
            if (!msg) {
                msg = document.createElement("label");
                msg.className = "form-label-pink text-danger mt-1";
                msg.textContent = "Harga minimal 1!";
                const anchor = el.closest(".input-group") || el.parentElement;
                anchor.insertAdjacentElement("afterend", msg);
            }
        }
    });
});

// Berat input mask (digits + single separator , or ., up to 3 decimals)
document.querySelectorAll("input[id='berat']").forEach((el) => {
    el.addEventListener("input", () => {
        let v = el.value.replace(/[^\d,.]/g, "");
        const firstSep = v.search(/[.,]/);
        if (firstSep !== -1) {
            const whole = v.slice(0, firstSep);
            let rest = v.slice(firstSep + 1).replace(/[.,]/g, "");
            v = whole + "," + rest.slice(0, 3);
        }
        if (el.value !== v) {
            el.value = v;
        }
    });
});

// Required dropdown message (system-wide; fires on novalidate forms where native is off)
document.querySelectorAll("form").forEach((form) => {
    const selects = [...form.querySelectorAll("select[required]")];
    if (!selects.length) {
        return;
    }

    selects.forEach((sel) => {
        sel.addEventListener("change", () => {
            if (sel._reqMsg) {
                sel._reqMsg.textContent = "";
            }
        });

        form.addEventListener("submit", (e) => {
            let invalid = false;
            selects.forEach((s) => {
                if (s.value === "") {
                    invalid = true;
                    const label =
                        s.closest(".mb-2,.mb-3,.mb-4")
                            ?.querySelector("label")
                            ?.textContent?.trim() || "";
                    const text = label ? `Pilih ${label}.` : "Wajib diisi.";
                    if (!s._reqMsg) {
                        s._reqMsg = document.createElement("div");
                        s._reqMsg.className = "text-danger small mt-1";
                        s.insertAdjacentElement("afterend", s._reqMsg);
                    }
                    s._reqMsg.textContent = text;
                } else if (s._reqMsg) {
                    s._reqMsg.textContent = "";
                }
            });
            if (invalid) {
                e.preventDefault();
            }
        });
    });
});

// Suppress native bubble + show custom msg on required dropdowns (non-novalidate forms)
document.querySelectorAll("select[required]").forEach((sel) => {
    sel.addEventListener("invalid", (e) => {
        e.preventDefault();
        const label =
            sel.closest(".mb-2,.mb-3,.mb-4")
                ?.querySelector("label")
                ?.textContent?.trim() || "";
        if (!sel._reqMsg) {
            sel._reqMsg = document.createElement("div");
            sel._reqMsg.className = "text-danger small mt-1";
            sel.insertAdjacentElement("afterend", sel._reqMsg);
        }
        sel._reqMsg.textContent = label ? `Pilih ${label}.` : "Wajib diisi.";
    });
});

// Required textarea message (non-novalidate forms), e.g. alamat lengkap
document.querySelectorAll("textarea[required]").forEach((ta) => {
    ta.addEventListener("invalid", (e) => {
        e.preventDefault();
        const label =
            ta.closest(".mb-2,.mb-3,.mb-4")
                ?.querySelector("label")
                ?.textContent?.trim() ||
            (ta.id ? document.querySelector(`label[for="${ta.id}"]`)?.textContent?.trim() : "") ||
            "";
        if (!ta._reqMsg) {
            ta._reqMsg = document.createElement("div");
            ta._reqMsg.className = "text-danger small mt-1";
            ta.insertAdjacentElement("afterend", ta._reqMsg);
        }
        ta._reqMsg.textContent = label ? `${label} wajib diisi.` : "Wajib diisi.";
    });
    ta.addEventListener("input", () => {
        if (ta._reqMsg) {
            ta._reqMsg.textContent = "";
        }
    });
});

// Alamat form: province -> city -> district -> village dropdowns
(() => {
    const provSel = document.getElementById("id_provinsi");
    const citySel = document.getElementById("id_kota");
    const distSel = document.getElementById("id_kecamatan");
    const villSel = document.getElementById("id_kelurahan");
    if (!provSel || !citySel || !distSel || !villSel) return;

    const cityUrlTpl = provSel.dataset.citiesUrl || null;
    const distUrlTpl = citySel.dataset.districtsUrl || null;
    const villUrlTpl = villSel.dataset.villagesUrl || null;
    const labelFor = (name) => document.querySelector(`input[name='${name}']`);

    const optionOf = (item) => {
        const o = document.createElement("option");
        o.value = item.code ?? item.id;
        o.textContent = item.name;
        return o;
    };

    const syncLabels = () => {
        const p = provSel.selectedOptions[0];
        const c = citySel.selectedOptions[0];
        const d = distSel.selectedOptions[0];
        const v = villSel.selectedOptions[0];
        if (labelFor("provinsi"))
            labelFor("provinsi").value = p && p.value ? p.textContent : "";
        if (labelFor("kota"))
            labelFor("kota").value = c && c.value ? c.textContent : "";
        if (labelFor("kecamatan"))
            labelFor("kecamatan").value = d && d.value ? d.textContent : "";
        if (labelFor("kelurahan"))
            labelFor("kelurahan").value = v && v.value ? v.textContent : "";
    };

    const fill = (sel, items, saved) => {
        sel.innerHTML = '<option value="">Pilih</option>';
        items.forEach((it) => sel.appendChild(optionOf(it)));
        if (saved) sel.value = saved;
        syncLabels();
    };

    const fetchList = (url) =>
        fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } }).then(
            (res) => res.json(),
        );

    const loadCities = () => {
        const id = provSel.value;
        citySel.innerHTML = '<option value="">Pilih</option>';
        distSel.innerHTML = '<option value="">Pilih</option>';
        villSel.innerHTML = '<option value="">Pilih</option>';
        syncLabels();
        if (!id || !cityUrlTpl) return;
        fetchList(cityUrlTpl.replace(":id", id))
            .then((cities) => {
                fill(citySel, cities, citySel.dataset.saved);
                if (citySel.value) loadDistricts();
            })
            .catch(() => {});
    };

    const loadDistricts = () => {
        const id = citySel.value;
        distSel.innerHTML = '<option value="">Pilih</option>';
        villSel.innerHTML = '<option value="">Pilih</option>';
        syncLabels();
        if (!id || !distUrlTpl) return;
        fetchList(distUrlTpl.replace(":id", id))
            .then((districts) => {
                fill(distSel, districts, distSel.dataset.saved);
                if (distSel.value) loadVillages();
            })
            .catch(() => {});
    };

    const loadVillages = () => {
        const id = distSel.value;
        villSel.innerHTML = '<option value="">Pilih</option>';
        syncLabels();
        if (!id || !villUrlTpl) return;
        fetchList(villUrlTpl.replace(":id", id))
            .then((villages) => fill(villSel, villages, villSel.dataset.saved))
            .catch(() => {});
    };

    provSel.addEventListener("change", loadCities);
    citySel.addEventListener("change", loadDistricts);
    distSel.addEventListener("change", loadVillages);
    villSel.addEventListener("change", syncLabels);
    if (provSel.value) loadCities();
})();

// Checkout: shipping rate + discount + live total
(() => {
    const form = document.getElementById("checkout-form");
    if (!form) return;

    const fmt = (n) => "Rp " + Number(n || 0).toLocaleString("id-ID");
    const subtotalRaw = () =>
        Number(document.getElementById("subtotal-raw").value || 0);
    const ongkirRaw = () =>
        Number(document.getElementById("ongkir-raw").value || 0);
    const diskonRaw = () =>
        Number(document.getElementById("diskon-raw").value || 0);
    const diskonPersenLabel = document.getElementById("diskon-persen-label");
    const diskonLabel = document.getElementById("diskon-label");
    const setDiskonPersen = (persen) => {
        if (diskonPersenLabel)
            diskonPersenLabel.textContent = persen ? `(${persen}%)` : "";
    };
    const setDiskonLabel = (isMember) => {
        if (diskonLabel)
            diskonLabel.textContent = isMember ? "Diskon Member" : "Diskon";
    };
    const memberDiskon = () => Number(form.dataset.memberDiskon || 0);
    const memberRow = document.getElementById("sum-member-diskon");
    const setMemberRow = (show) => {
        if (memberRow)
            memberRow.closest(".summary-row").style.display = show
                ? ""
                : "none";
    };

    const refreshTotal = () => {
        const total = Math.max(0, subtotalRaw() - diskonRaw() + ongkirRaw());
        document.getElementById("sum-subtotal").textContent =
            fmt(subtotalRaw());
        document.getElementById("sum-diskon").textContent =
            diskonRaw() > 0 ? "- " + fmt(diskonRaw()) : "-";
        document.getElementById("sum-ongkir").textContent =
            ongkirRaw() > 0 ? fmt(ongkirRaw()) : "-";
        document.getElementById("sum-total").textContent = fmt(total);
    };

    const selectedAddress = () =>
        form.querySelector("input[name='id_alamat']:checked")?.value;

    form.addEventListener("submit", (e) => {
        const service = document.getElementById("shipping-service")?.value;
        const cost = document.getElementById("shipping-cost")?.value;

        if (!service || !cost) {
            e.preventDefault();
            const box = document.getElementById("shipping-error");
            box.textContent = "Pilih layanan ongkir terlebih dahulu.";
            box.style.display = "block";
            return;
        }

        const btn = document.getElementById("bayar-submit");
        if (btn) {
            btn.disabled = true;
            btn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1"></span> Memproses…';
        }
    });

    document.getElementById("cek-ongkir").addEventListener("click", () => {
        const id = selectedAddress();
        const box = document.getElementById("shipping-options");
        if (!id) {
            box.innerHTML =
                '<span class="text-danger">Pilih alamat terlebih dahulu.</span>';
            return;
        }
        box.innerHTML =
            '<span class="text-muted">Mengecek Ongkos Kirim…</span>';
        const csrf = form.querySelector("input[name='_token']")?.value || "";
        const rateUrl = form.dataset.rateUrl || "";
        fetch(rateUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrf,
            },
            body: JSON.stringify({ id_alamat: id }),
        })
            .then((res) => res.json())
            .then((data) => {
                if (!data.options)
                    throw new Error(data.message || "Tidak ada ongkir");
                box.innerHTML = "";
                data.options.forEach((o) => {
                    const el = document.createElement("label");
                    el.className =
                        "form-check d-flex align-items-start gap-2 mb-2";
                    el.innerHTML =
                        `<input class="form-check-input ship-opt" type="radio" name="shipping_service" ` +
                        `value="${o.id}" data-cost="${o.cost}"> ` +
                        `<span class="flex-grow-1"><span class="d-block fw-semibold">${o.service}</span>` +
                        `<small class="text-muted">${o.description || ""} ${o.etd ? "(" + o.etd + ")" : ""}</small></span>` +
                        `<strong class="text-nowrap">${fmt(o.cost)}</strong>`;
                    box.appendChild(el);
                });
            })
            .catch((err) => {
                box.innerHTML =
                    '<span class="text-danger">' +
                    (err.message || "Gagal memuat ongkir.") +
                    "</span>";
            });
    });

    document.addEventListener("click", (e) => {
        const opt = e.target.closest(".ship-opt");
        if (!opt) return;
        document.getElementById("shipping-service").value = opt.value;
        document.getElementById("shipping-cost").value = opt.dataset.cost;
        document.getElementById("ongkir-raw").value = opt.dataset.cost;
        const errEl = document.getElementById("shipping-error");
        if (errEl) errEl.style.display = "none";
        refreshTotal();
    });

    document.getElementById("terapkan-diskon").addEventListener("click", () => {
        const input = document.getElementById("kode-diskon");
        const info = document.getElementById("diskon-info");
        const csrf = form.querySelector("input[name='_token']")?.value || "";
        const diskonUrl = form.dataset.diskonUrl || "";

        const kode = input.value.trim();
        if (!kode) {
            document.getElementById("diskon-raw").value = memberDiskon();
            setMemberRow(true);
            info.innerHTML =
                '<span class="text-danger">Masukkan kode diskon terlebih dahulu.</span>';
            refreshTotal();
            return;
        }

        info.innerHTML = '<span class="text-muted">Memverifikasi…</span>';

        fetch(diskonUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN": csrf,
            },
            body: JSON.stringify({ kode_diskon: kode }),
        })
            .then((res) => {
                const ct = res.headers.get("content-type") || "";
                if (!ct.includes("application/json")) {
                    throw new Error("Gagal memverifikasi diskon. Coba lagi.");
                }
                return res.json().then((data) => ({ ok: res.ok, data }));
            })
            .then(({ ok, data }) => {
                if (!ok || !data.code)
                    throw new Error(data.message || "Kode diskon tidak valid.");
                document.getElementById("diskon-raw").value = data.nominal;
                setMemberRow(false);
                setDiskonPersen(data.persen);
                setDiskonLabel(false);
                document.getElementById("hapus-diskon").style.display = "";
                info.innerHTML =
                    '<span class="text-success">Diskon: ' +
                    (data.persen ? data.persen + "% (" : "") +
                    fmt(data.nominal) +
                    (data.persen ? ")" : "") +
                    "</span>";
                refreshTotal();
            })
            .catch((err) => {
                document.getElementById("diskon-raw").value = memberDiskon();
                setMemberRow(true);
                setDiskonPersen(memberDiskon() > 0 ? 10 : 0);
                setDiskonLabel(memberDiskon() > 0);
                document.getElementById("hapus-diskon").style.display = "none";
                info.innerHTML =
                    '<span class="text-danger">' +
                    (err.message || "Gagal memverifikasi diskon.") +
                    "</span>";
                refreshTotal();
            });
    });

    const hapusBtn = document.getElementById("hapus-diskon");
    if (hapusBtn) {
        hapusBtn.addEventListener("click", () => {
            document.getElementById("kode-diskon").value = "";
            document.getElementById("diskon-raw").value = memberDiskon();
            setMemberRow(true);
            setDiskonPersen(memberDiskon() > 0 ? 10 : 0);
            setDiskonLabel(memberDiskon() > 0);
            hapusBtn.style.display = "none";
            document.getElementById("diskon-info").innerHTML =
                '<span class="text-muted">Kode diskon dihapus.</span>';
            refreshTotal();
        });
    }

    refreshTotal();
})();

// Snap.js payment popup (checkout show page)
(() => {
    const btn = document.getElementById("bayar-button");
    if (!btn) return;

    const clientKey = btn.dataset.clientKey;
    const sdkBase =
        btn.dataset.prod === "1"
            ? "https://app.midtrans.com"
            : "https://app.sandbox.midtrans.com";

    const loadSnap = () =>
        new Promise((resolve, reject) => {
            if (window.snap) return resolve();
            const s = document.createElement("script");
            s.src = sdkBase + "/snap/snap.js";
            s.dataset.clientKey = clientKey;
            s.onload = () => resolve();
            s.onerror = reject;
            document.head.appendChild(s);
        });

    btn.addEventListener("click", () => {
        const statusUrl = btn.dataset.statusUrl;
        const pollStatus = () => {
            if (!statusUrl) return done();
            const maxTries = 20;
            let tries = 0;
            const tick = () => {
                fetch(statusUrl)
                    .then((res) => res.json())
                    .then((data) => {
                        if (data.status && data.status !== "pending") return done();
                        if (++tries < maxTries) return setTimeout(tick, 1000);
                        done();
                    })
                    .catch(done);
            };
            tick();
        };

        const done = () =>
            btn.dataset.redirectUrl
                ? (window.location.href = btn.dataset.redirectUrl)
                : window.location.reload();
        const form = btn.closest("form");
        const tokenUrl = form ? form.dataset.tokenUrl : "";

        const getToken = () =>
            new Promise((resolve, reject) => {
                if (btn.dataset.checkoutToken)
                    return resolve(btn.dataset.checkoutToken);
                if (!tokenUrl)
                    return reject(new Error("Token tidak tersedia."));
                const csrf =
                    form.querySelector("input[name='_token']")?.value || "";
                fetch(tokenUrl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": csrf,
                    },
                })
                    .then((res) => {
                        const ct = res.headers.get("content-type") || "";
                        if (!ct.includes("application/json")) {
                            throw new Error(
                                "Gagal memuat pembayaran. Coba lagi.",
                            );
                        }
                        return res
                            .json()
                            .then((data) => ({ ok: res.ok, data }));
                    })
                    .then(({ ok, data }) => {
                        if (!ok || !data.token)
                            throw new Error(
                                data.message || "Gagal memuat pembayaran.",
                            );
                        resolve(data.token);
                    })
                    .catch(reject);
            });

        const errBox = () =>
            btn
                .closest("div")
                .insertAdjacentHTML(
                    "beforeend",
                    '<p class="text-danger small mt-2">Gagal memuat pembayaran. Coba lagi.</p>',
                );

        getToken()
            .then((t) =>
                loadSnap().then(() => {
                    window.snap.pay(t, {
                        onSuccess: pollStatus,
                        onPending: pollStatus,
                        onError: done,
                        onClose: done,
                    });
                }),
            )
            .catch(() => errBox());
    });
})();

// Kode Barang Duplicate Check
(function () {
    const input = document.getElementById("kode_barang");
    if (!input || !input.dataset.checkUrl) {
        return;
    }

    const feedback = document.getElementById("kode_barang_feedback");
    let timer = null;

    input.addEventListener("input", () => {
        clearTimeout(timer);
        const kode = input.value.trim();
        feedback.textContent = "";

        if (!kode) {
            return;
        }

        timer = setTimeout(() => {
            const url = new URL(input.dataset.checkUrl, window.location.origin);
            url.searchParams.set("kode", kode);
            url.searchParams.set("exclude", input.dataset.exclude ?? "");

            fetch(url)
                .then((r) => r.json())
                .then((d) => {
                    if (d.exists) {
                        feedback.textContent =
                            "Kode Barang sudah digunakan, silakan gunakan kode lain!";
                    }
                })
                .catch(() => {});
        }, 400);
    });
})();

// Auto Kode Barang (BRG-kategori+brand+seq)
(function () {
    const input = document.getElementById("kode_barang");
    if (!input || !input.dataset.checkUrl) {
        return;
    }

    const katSel = document.getElementById("id_kategori");
    const brandSel = document.getElementById("id_brand");
    if (!katSel || !brandSel) {
        return;
    }

    const checkUrl = input.dataset.checkUrl;

    function exists(kode) {
        const url = new URL(checkUrl, window.location.origin);
        url.searchParams.set("kode", kode);
        url.searchParams.set("exclude", input.dataset.exclude ?? "");
        return fetch(url)
            .then((r) => r.json())
            .then((d) => !!d.exists)
            .catch(() => false);
    }

    let busy = false;
    async function fill(kat, brand, seq) {
        if (busy || !kat || !brand) {
            return;
        }
        busy = true;
        seq = parseInt(seq, 10) || 1;
        let kode = `BRG-${kat}${brand}${String(seq).padStart(3, "0")}`;
        while (await exists(kode)) {
            seq += 1;
            kode = `BRG-${kat}${brand}${String(seq).padStart(3, "0")}`;
        }
        input.value = kode;
        busy = false;
    }

    if (input.dataset.nextSeq) {
        // Tambah: readonly -> auto-fill on selection
        const tryFill = () => fill(katSel.value, brandSel.value, input.dataset.nextSeq);
        katSel.addEventListener("change", tryFill);
        brandSel.addEventListener("change", tryFill);
        tryFill();
    } else {
        // Edit: editable -> recompute only when field still matches auto pattern
        const origPrefix = `BRG-${input.dataset.kategori}${input.dataset.brand}`;
        const origSeq = input.value.startsWith(origPrefix)
            ? input.value.slice(origPrefix.length)
            : "";
        if (!/^\d+$/.test(origSeq)) {
            return; // not auto-generated; don't auto-touch
        }
        const isAutoPattern = () =>
            input.value.startsWith(origPrefix) &&
            /^\d+$/.test(input.value.slice(origPrefix.length));
        const tryRebuild = () => {
            if (isAutoPattern()) {
                fill(katSel.value, brandSel.value, origSeq);
            }
        };
        katSel.addEventListener("change", tryRebuild);
        brandSel.addEventListener("change", tryRebuild);
    }
})();

// Confirm modal for add/edit forms (data-confirm)
(() => {
    let modal = null;
    let pending = null;

    const ensureModal = () => {
        if (modal) {
            return modal;
        }
        modal = document.createElement("div");
        modal.className = "modal fade";
        modal.tabIndex = -1;
        modal.setAttribute("aria-hidden", "true");
        modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-pink">
                    <div class="modal-header">
                        <h1 class="modal-title fs-4">Konfirmasi</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center my-4" data-confirm-msg></div>
                    <div class="modal-footer mx-auto">
                        <button type="button" class="btn btn-green" data-confirm-ok>YA</button>
                        <button type="button" class="btn btn-delete" data-bs-dismiss="modal">BATAL</button>
                    </div>
                </div>
            </div>`;
        document.body.appendChild(modal);

        modal.querySelector("[data-confirm-ok]").addEventListener("click", () => {
            if (pending) {
                const form = pending.form;
                form._confirmDone = true;
                form.requestSubmit();
                pending = null;
            }
            if (window.bootstrap) {
                window.bootstrap.Modal.getInstance(modal)?.hide();
            }
        });

        return modal;
    };

    const showConfirm = (msg) => {
        const m = ensureModal();
        m.querySelector("[data-confirm-msg]").textContent = `Apakah Anda yakin ingin ${msg}`;
        if (window.bootstrap) {
            window.bootstrap.Modal.getOrCreateInstance(m).show();
        }
    };

    document.querySelectorAll("form[data-confirm]").forEach((form) => {
        form.addEventListener("submit", (e) => {
            if (form._confirmDone) {
                form._confirmDone = false;
                return;
            }
            e.preventDefault();
            pending = { form, msg: form.dataset.confirm };
            showConfirm(pending.msg);
        });
    });
})();

// Searchable selects: native <select> stays the source of truth; a filter
// input + option list wrap it. Repopulation (fill()) re-renders via observer.
(() => {
    const renderOptions = (sel) => {
        const items = [];
        sel.querySelectorAll("option").forEach((o) => {
            items.push({ value: o.value, label: o.textContent.trim() });
        });
        return items;
    };

    document.querySelectorAll("select.searchable").forEach((sel) => {
        sel.classList.add("searchable-bound");
        const hidden = sel.closest(".searchable-wrap");
        if (hidden) return;

        const wrap = document.createElement("div");
        wrap.className = "searchable-wrap mb-1";

        const box = document.createElement("div");
        box.className = "searchable-box position-relative";

        const input = document.createElement("input");
        input.type = "text";
        input.className = "form-control form-control-pink searchable-input pe-5";
        input.autocomplete = "off";
        input.placeholder = "Cari…";

        const caret = document.createElement("i");
        caret.className =
            "fa-solid fa-chevron-down position-absolute top-50 translate-middle-y end-0 me-3 text-muted";
        caret.style.pointerEvents = "none";

        const list = document.createElement("div");
        list.className =
            "searchable-list position-absolute start-0 end-0 bg-white border rounded shadow-sm d-none";
        list.style.zIndex = "1080";
        list.style.maxHeight = "220px";
        list.style.overflowY = "auto";

        const display = () => {
            const o = sel.selectedOptions[0];
            input.value = o && o.value ? o.textContent.trim() : "";
        };

        const build = () => {
            list.innerHTML = "";
            const items = renderOptions(sel);
            const q = input.value.toLowerCase();
            items.forEach((it) => {
                if (q && !it.label.toLowerCase().includes(q)) return;
                const btn = document.createElement("button");
                btn.type = "button";
                btn.className =
                    "searchable-opt d-block w-100 text-start border-0 bg-transparent px-3 py-2";
                btn.textContent = it.label;
                btn.dataset.value = it.value;
                if (it.value === sel.value) btn.classList.add("fw-bold", "text-pink");
                btn.addEventListener("mousedown", (e) => e.preventDefault());
                btn.addEventListener("click", () => {
                    sel.value = it.value;
                    sel.dispatchEvent(new Event("change", { bubbles: true }));
                    display();
                    list.classList.add("d-none");
                    input.blur();
                });
                list.appendChild(btn);
            });
        };

        input.addEventListener("focus", () => {
            build();
            list.classList.remove("d-none");
        });
        input.addEventListener("input", build);
        input.addEventListener("keydown", (e) => {
            if (e.key === "Escape") list.classList.add("d-none");
        });

        document.addEventListener("click", (e) => {
            if (!wrap.contains(e.target)) list.classList.add("d-none");
        });

        sel.style.display = "none";
        sel.insertAdjacentElement("afterend", wrap);
        wrap.appendChild(box);
        box.appendChild(input);
        box.appendChild(caret);
        box.appendChild(list);
        display();

        const observer = new MutationObserver(() => {
            display();
            build();
        });
        observer.observe(sel, { childList: true, attributes: true });
    });
})();
