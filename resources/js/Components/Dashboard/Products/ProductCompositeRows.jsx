// RF-002: Ekstraksi komponen form produk bersama.
import React from "react";
import Input from "@/Components/Dashboard/Input";
import InputSelect from "@/Components/Dashboard/InputSelect";
import { IconPackages, IconTrash } from "@tabler/icons-react";

export default function ProductCompositeRows({
    data, setData, errors, products, excludedProductId = null, className,
}) {
    const addComponent = () => {
        setData("components", [
            ...data.components,
            { component_product_id: "", qty: 1 },
        ]);
    };

    const updateComponent = (index, field, value) => {
        const components = data.components.map((c, i) =>
            i === index ? { ...c, [field]: value } : c
        );
        setData("components", components);
    };

    const removeComponent = (index) => {
        setData(
            "components",
            data.components.filter((_, i) => i !== index)
        );
    };

    const availableProducts = (selectedIndex) =>
        products.filter(
            (p) =>
                !p.is_composite &&
                p.id !== excludedProductId &&
                !data.components.some(
                    (c, i) =>
                        i !== selectedIndex &&
                        c.component_product_id === p.id
                )
        );

    const estimatedSellPrice = data.is_composite
        ? data.components.reduce((total, c) => {
              const product = products.find(
                  (p) => p.id === Number(c.component_product_id)
              );
              return product
                  ? total + product.sell_price * (Number(c.qty) || 0)
                  : total;
          }, 0)
        : data.sell_price;

    return (
        <div className={className}>
            <div className="flex items-center justify-between mb-4">
                <h3 className="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                    <IconPackages size={18} />
                    Komponen Paket
                </h3>
                <button
                    type="button"
                    onClick={addComponent}
                    className="text-sm text-primary-600 hover:text-primary-700 font-medium"
                >
                    + Tambah Komponen
                </button>
            </div>
            {errors.components && (
                <p className="text-sm text-danger-600 mb-2">
                    {errors.components}
                </p>
            )}
            {data.components.length === 0 && (
                <p className="text-sm text-slate-500">
                    Belum ada komponen. Stok dan harga jual
                    dihitung otomatis dari komponen.
                </p>
            )}
            <div className="space-y-3">
                {data.components.map((component, index) => (
                    <div
                        key={index}
                        className="flex items-end gap-3"
                    >
                        <div className="flex-1">
                            <InputSelect
                                data={availableProducts(
                                    index
                                )}
                                selected={
                                    products.find(
                                        (p) =>
                                            p.id ===
                                            Number(
                                                component.component_product_id
                                            )
                                    ) || null
                                }
                                setSelected={(
                                    value
                                ) =>
                                    updateComponent(
                                        index,
                                        "component_product_id",
                                        value?.id ?? ""
                                    )
                                }
                                placeholder="Pilih produk komponen"
                                errors={
                                    errors[
                                        `components.${index}.component_product_id`
                                    ]
                                }
                                searchable={true}
                                displayKey="title"
                            />
                        </div>
                        <div className="w-28">
                            <Input
                                type="number"
                                min="1"
                                value={component.qty}
                                onChange={(e) =>
                                    updateComponent(
                                        index,
                                        "qty",
                                        e.target.value
                                    )
                                }
                                errors={
                                    errors[
                                        `components.${index}.qty`
                                    ]
                                }
                                placeholder="Qty"
                            />
                        </div>
                        <button
                            type="button"
                            onClick={() =>
                                removeComponent(index)
                            }
                            className="p-2.5 rounded-xl text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-950/30 transition-colors"
                        >
                            <IconTrash size={18} />
                        </button>
                    </div>
                ))}
            </div>
            {data.is_composite && estimatedSellPrice > 0 && (
                <p className="mt-4 text-sm text-slate-500 dark:text-slate-400">
                    Estimasi harga jual dari komponen:{" "}
                    <span className="font-semibold text-slate-700 dark:text-slate-200">
                        Rp{" "}
                        {estimatedSellPrice.toLocaleString(
                            "id-ID"
                        )}
                    </span>
                </p>
            )}
        </div>
    );
}
