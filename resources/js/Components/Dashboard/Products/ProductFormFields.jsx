// RF-002: Ekstraksi komponen form produk bersama.
import React from "react";
import Input from "@/Components/Dashboard/Input";
import InputSelect from "@/Components/Dashboard/InputSelect";
import Textarea from "@/Components/Dashboard/TextArea";
import { IconBarcode } from "@tabler/icons-react";

export default function ProductFormFields({
    data, setData, errors, categories, selectedCategory, onCategoryChange, placeholders,
}) {
    return (
        <div className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
            <h3 className="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4 flex items-center gap-2">
                <IconBarcode size={18} />
                Informasi Dasar
            </h3>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="md:col-span-2">
                    <InputSelect
                        label="Kategori"
                        data={categories}
                        selected={selectedCategory}
                        setSelected={onCategoryChange}
                        placeholder="Pilih kategori"
                        errors={errors.category_id}
                        searchable={true}
                        displayKey="name"
                    />
                </div>
                <Input
                    type="text"
                    label="Barcode"
                    value={data.barcode}
                    onChange={(e) =>
                        setData("barcode", e.target.value)
                    }
                    errors={errors.barcode}
                    placeholder={placeholders.barcode}
                />
                <Input
                    type="text"
                    label="SKU"
                    value={data.sku}
                    onChange={(e) => setData("sku", e.target.value)}
                    errors={errors.sku}
                    placeholder={placeholders.sku}
                />
                <Input
                    type="text"
                    label="Nama Produk"
                    value={data.title}
                    onChange={(e) =>
                        setData("title", e.target.value)
                    }
                    errors={errors.title}
                    placeholder={placeholders.title}
                />
                <div className="md:col-span-2">
                    <Textarea
                        label="Deskripsi"
                        placeholder="Deskripsi produk (opsional)"
                        errors={errors.description}
                        onChange={(e) =>
                            setData(
                                "description",
                                e.target.value
                            )
                        }
                        value={data.description}
                        rows={3}
                    />
                </div>
            </div>
        </div>
    );
}
