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
    const target = document.getElementById(input.dataset.tbody);
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
    .querySelectorAll("input[name='harga'], input[name='harga_ukuran']")
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

// Alamat form: Klikresi province -> city -> district dropdowns
(() => {
    const provSel = document.getElementById("id_provinsi");
    const citySel = document.getElementById("id_kota");
    const distSel = document.getElementById("id_kecamatan");
    if (!provSel || !citySel || !distSel) return;

    const cityUrlTpl = provSel.dataset.citiesUrl || null;
    const distUrlTpl = citySel.dataset.districtsUrl || null;
    const labelFor = (name) => document.querySelector(`input[name='${name}']`);

    const optionOf = (item) => {
        const o = document.createElement("option");
        o.value = item.id;
        o.textContent = item.name;
        return o;
    };

    const syncLabels = () => {
        const p = provSel.selectedOptions[0];
        const c = citySel.selectedOptions[0];
        const d = distSel.selectedOptions[0];
        if (labelFor("provinsi"))
            labelFor("provinsi").value = p && p.value ? p.textContent : "";
        if (labelFor("kota"))
            labelFor("kota").value = c && c.value ? c.textContent : "";
        if (labelFor("kecamatan"))
            labelFor("kecamatan").value = d && d.value ? d.textContent : "";
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
        syncLabels();
        if (!id || !distUrlTpl) return;
        fetchList(distUrlTpl.replace(":id", id))
            .then((districts) =>
                fill(distSel, districts, distSel.dataset.saved),
            )
            .catch(() => {});
    };

    provSel.addEventListener("change", loadCities);
    citySel.addEventListener("change", loadDistricts);
    distSel.addEventListener("change", syncLabels);
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
        if (diskonLabel) diskonLabel.textContent = isMember ? "Diskon Member" : "Diskon";
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
        box.innerHTML = '<span class="text-muted">Menghitung ongkir…</span>';
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
                    el.className = "form-check d-block mb-1";
                    el.innerHTML =
                        `<input class="form-check-input ship-opt" type="radio" name="shipping_service" ` +
                        `value="${o.id}" data-cost="${o.cost}"> ` +
                        `<span>${o.service}</span> ` +
                        `<small class="text-muted">${o.description || ""} ${o.etd ? "(" + o.etd + ")" : ""}</small> ` +
                        `<strong class="float-end">${fmt(o.cost)}</strong>`;
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
        const redirectUrl = btn.dataset.redirectUrl;
        const done = () =>
            redirectUrl
                ? (window.location.href = redirectUrl)
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
                        onSuccess: done,
                        onPending: done,
                        onError: done,
                        onClose: done,
                    });
                }),
            )
            .catch(() => errBox());
    });
})();
