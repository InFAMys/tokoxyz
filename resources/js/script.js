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
    const baseUrl = new URL(input.closest("form")?.action || window.location.href);
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
            prices[selected] !== undefined ? formatPrice(prices[selected]) : defaultPrice;
    });
}

// Keranjang max-stok check (styled, replaces native 'max' bubble)
document.querySelectorAll("form[novalidate] input[name='jumlah_barang']").forEach((input) => {
    const error = input.closest("div")?.querySelector("[data-jumlah-error]");
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

document.querySelectorAll("input[name='harga'], input[name='harga_ukuran']").forEach((el) => {
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
