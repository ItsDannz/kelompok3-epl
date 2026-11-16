// RF-032: Ekstraksi formatter Rupiah bersama.
export const formatRupiah = (value = 0) =>
    Number(value || 0).toLocaleString("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    });
