// RF-002: Ekstraksi komponen form produk bersama.
import React from "react";
import Input from "@/Components/Dashboard/Input";
import InputSelect from "@/Components/Dashboard/InputSelect";
import { IconRulerMeasure, IconTrash } from "@tabler/icons-react";

export default function ProductUnitRows({ data, setData, errors, units }) {
    const addUnitRow = () => {
        const firstUnused = units.find(
            (u) => !data.units.some((row) => row.unit_id === u.id)
        );
        if (!firstUnused) return;
        setData("units", [
            ...data.units,
            {
                unit_id: firstUnused.id,
                is_base: data.units.length === 0,
                conversion_factor: data.units.length === 0 ? 1 : "",
                sell_price: "",
                barcode: "",
            },
        ]);
    };

    const updateUnitRow = (index, field, value) => {
        const rows = data.units.map((r, i) =>
            i === index ? { ...r, [field]: value } : r
        );
        if (field === "is_base" && value) {
            rows.forEach((r, i) => {
                if (i !== index) r.is_base = false;
            });
        }
        setData("units", rows);
    };

    const removeUnitRow = (index) => {
        setData("units", data.units.filter((_, i) => i !== index));
    };

    const availableUnits = (selectedIndex) =>
        units.filter(
            (u) =>
                !data.units.some(
                    (r, i) => i !== selectedIndex && r.unit_id === u.id
                )
        );

    return (
        <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
            <div className="flex items-center justify-between mb-4">
                <h3 className="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                    <IconRulerMeasure size={18} />
                    Satuan
                </h3>
                <button
                    type="button"
                    onClick={addUnitRow}
                    disabled={
                        data.units.length >= units.length
                    }
                    className="text-sm text-primary-600 hover:text-primary-700 font-medium disabled:opacity-40"
                >
                    + Tambah Satuan
                </button>
            </div>
            {errors.units && (
                <p className="text-sm text-danger-600 mb-2">
                    {errors.units}
                </p>
            )}
            {data.units.length === 0 && (
                <p className="text-sm text-slate-500">
                    Satuan dasar otomatis dibuat jika tidak
                    ditambahkan. Tambahkan satuan (box, kg,
                    dll) untuk penjualan multi-satuan.
                </p>
            )}
            <div className="space-y-3">
                {data.units.map((row, index) => (
                    <div
                        key={index}
                        className="flex items-end gap-3"
                    >
                        <div className="flex-1">
                            <InputSelect
                                data={availableUnits(index)}
                                selected={
                                    units.find(
                                        (u) =>
                                            u.id ===
                                            row.unit_id
                                    ) || null
                                }
                                setSelected={(value) =>
                                    updateUnitRow(
                                        index,
                                        "unit_id",
                                        value?.id ?? ""
                                    )
                                }
                                placeholder="Pilih satuan"
                                errors={
                                    errors[
                                        `units.${index}.unit_id`
                                    ]
                                }
                                searchable={true}
                                displayKey="code"
                            />
                        </div>
                        <div className="w-36">
                            <Input
                                type="number"
                                min="0.0001"
                                step="0.0001"
                                value={
                                    row.conversion_factor
                                }
                                onChange={(e) =>
                                    updateUnitRow(
                                        index,
                                        "conversion_factor",
                                        e.target.value
                                    )
                                }
                                errors={
                                    errors[
                                        `units.${index}.conversion_factor`
                                    ]
                                }
                                placeholder="Konversi"
                                disabled={row.is_base}
                            />
                        </div>
                        <div className="w-40">
                            <Input
                                type="number"
                                min="0"
                                value={row.sell_price}
                                onChange={(e) =>
                                    updateUnitRow(
                                        index,
                                        "sell_price",
                                        e.target.value
                                    )
                                }
                                errors={
                                    errors[
                                        `units.${index}.sell_price`
                                    ]
                                }
                                placeholder="Harga jual"
                            />
                        </div>
                        <div className="w-44">
                            <Input
                                type="text"
                                value={row.barcode}
                                onChange={(e) =>
                                    updateUnitRow(
                                        index,
                                        "barcode",
                                        e.target.value
                                    )
                                }
                                errors={
                                    errors[
                                        `units.${index}.barcode`
                                    ]
                                }
                                placeholder="Barcode (opsional)"
                            />
                        </div>
                        <label className="flex items-center gap-1 pb-3 text-xs text-slate-600 dark:text-slate-300 whitespace-nowrap">
                            <input
                                type="checkbox"
                                checked={row.is_base}
                                onChange={(e) =>
                                    updateUnitRow(
                                        index,
                                        "is_base",
                                        e.target.checked
                                    )
                                }
                                className="rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                            />
                            Dasar
                        </label>
                        <button
                            type="button"
                            onClick={() =>
                                removeUnitRow(index)
                            }
                            className="p-2.5 rounded-xl text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-950/30 transition-colors"
                        >
                            <IconTrash size={18} />
                        </button>
                    </div>
                ))}
            </div>
        </div>
    );
}
