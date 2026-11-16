const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const { transformSync } = require('esbuild');
const React = require('react');
const root = path.resolve(__dirname, '../..');

// RF-002: Ekstraksi komponen form produk bersama; uji kontrak interaksi tanpa browser.
// Input/select di-stub; pengujian ini tidak menggantikan uji UI InputSelect atau submit Inertia.
function Input() {}
function InputSelect() {}
function Textarea() {}
const icon = () => null;
function load(relative) {
    const filename = path.join(root, 'resources/js', relative);
    const code = transformSync(fs.readFileSync(filename, 'utf8'), { loader: 'jsx', format: 'cjs' }).code;
    const module = { exports: {} };
    vm.runInNewContext(code, { module, exports: module.exports, require: (name) => {
        if (name === 'react') return React;
        if (name.endsWith('/Input')) return Input;
        if (name.endsWith('/InputSelect')) return InputSelect;
        if (name.endsWith('/TextArea')) return Textarea;
        if (name === '@tabler/icons-react') return { IconBarcode: icon, IconRulerMeasure: icon, IconTrash: icon, IconPackages: icon };
        throw new Error('Unexpected dependency: ' + name);
    } }, { filename });
    return module.exports;
}
function nodes(element) {
    if (Array.isArray(element)) return element.flatMap(nodes);
    if (!element || typeof element !== 'object') return [];
    return [element, ...nodes(element.props?.children)];
}
function text(element) {
    if (Array.isArray(element)) return element.map(text).join('');
    if (element == null || typeof element === 'boolean') return '';
    return typeof element === 'object' ? text(element.props?.children) : String(element);
}
const json = (value) => JSON.parse(JSON.stringify(value));
const units = [{ id: 1, code: 'PCS' }, { id: 2, code: 'BOX' }];
const ProductUnitRows = load('Components/Dashboard/Products/ProductUnitRows.jsx').default;
const ProductCompositeRows = load('Components/Dashboard/Products/ProductCompositeRows.jsx').default;
const ProductFormFields = load('Components/Dashboard/Products/ProductFormFields.jsx').default;

test('RF-002: tambah satuan pertama memilih PCS dan faktor dasar 1', () => {
    let update;
    const tree = ProductUnitRows({ data: { units: [] }, units, errors: {}, setData: (key, rows) => { update = [key, json(rows)]; } });
    nodes(tree).find(n => n.type === 'button' && text(n).includes('Tambah Satuan')).props.onClick();
    assert.deepEqual(update, ['units', [{ unit_id: 1, is_base: true, conversion_factor: 1, sell_price: '', barcode: '' }]]);
});

test('RF-002: satuan terpakai tidak ditawarkan ulang dan baris dapat dihapus', () => {
    let update;
    const data = { units: [{ unit_id: 1, is_base: true }, { unit_id: 2, is_base: false }] };
    const tree = ProductUnitRows({ data, units, errors: {}, setData: (key, rows) => { update = json(rows); } });
    const all = nodes(tree);
    assert.deepEqual(json(all.filter(n => n.type === InputSelect).map(n => n.props.data.map(u => u.id))), [[1], [2]]);
    assert.equal(all.find(n => n.type === 'button' && text(n).includes('Tambah Satuan')).props.disabled, true);
    all.filter(n => n.type === 'input' && n.props.type === 'checkbox')[1].props.onChange({ target: { checked: true } });
    assert.deepEqual(update.map(r => r.is_base), [false, true]);
    all.filter(n => n.type === 'button' && !text(n))[0].props.onClick();
    assert.deepEqual(update.map(r => r.unit_id), [2]);
});

test('RF-002: komposit mengecualikan dirinya, paket lain, dan komponen yang telah dipilih', () => {
    const products = [{ id: 1, title: 'A', sell_price: 10000 }, { id: 2, title: 'B', sell_price: 20000 },
        { id: 3, title: 'Self', sell_price: 0 }, { id: 4, title: 'Paket', is_composite: true }];
    let update;
    const tree = ProductCompositeRows({ data: { is_composite: true, components: [{ component_product_id: 1, qty: 2 }, { component_product_id: 2, qty: 1 }] },
        products, excludedProductId: 3, errors: {}, setData: (key, rows) => { update = json(rows); } });
    assert.deepEqual(json(nodes(tree).filter(n => n.type === InputSelect).map(n => n.props.data.map(p => p.id))), [[1], [2]]);
    assert.ok(text(tree).includes('40.000'));
    nodes(tree).find(n => n.type === 'button' && text(n).includes('Tambah Komponen')).props.onClick();
    assert.deepEqual(update[2], { component_product_id: '', qty: 1 });
});

test('RF-002: field dasar meneruskan perubahan nama dan error ke field yang sama', () => {
    const patches = [];
    const tree = ProductFormFields({ data: { title: 'Awal', barcode: 'B', sku: 'S', description: 'D' },
        setData: (...args) => patches.push(args), errors: { title: 'Nama wajib diisi' }, categories: [],
        selectedCategory: null, onCategoryChange: () => {}, placeholders: { title: 'Nama produk' } });
    const input = nodes(tree).find(n => n.type === Input && n.props.label === 'Nama Produk');
    assert.equal(input.props.value, 'Awal');
    assert.equal(input.props.errors, 'Nama wajib diisi');
    input.props.onChange({ target: { value: 'Baru' } });
    assert.deepEqual(patches, [['title', 'Baru']]);
});

// RF-032: Ekstraksi formatter Rupiah bersama; termasuk spasi NBSP dan pembulatan lama.
test('RF-032: format nominal, nilai kosong, string dan pecahan mempertahankan output', () => {
    const { formatRupiah } = load('Utils/formatRupiah.js');
    for (const [value, expected] of [[undefined, 'Rp\u00a00'], [null, 'Rp\u00a00'], ['', 'Rp\u00a00'],
        [0, 'Rp\u00a00'], [4496, 'Rp\u00a04.496'], ['4496', 'Rp\u00a04.496'],
        [5000, 'Rp\u00a05.000'], [504, 'Rp\u00a0504'], [4496.5, 'Rp\u00a04.497'], [-1000, '-Rp\u00a01.000']]) {
        assert.equal(formatRupiah(value), expected);
    }
});
