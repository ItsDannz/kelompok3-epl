import pathlib,re,json,csv,collections,hashlib,subprocess
root=pathlib.Path('/home/zdann/Documents/ChatGPT/epl/point-of-sales');out=root.parent/'output'/'audit-pos';out.mkdir(exist_ok=True)
tracked=subprocess.check_output(['git','ls-files'],cwd=root,text=True).splitlines(); rows=[]; dig=[]
for f in tracked:
 p=root/f
 if not p.is_file():continue
 raw=p.read_bytes();s=raw.decode(errors='replace');lines=s.splitlines()
 cat='kode' if p.suffix in ['.php','.js','.jsx','.css'] else ('data/config' if p.suffix in ['.json','.xml'] else 'dokumen/aset')
 if f.startswith('public/screenshots/') or p.suffix in ['.png','.jpg','.jpeg','.svg','.ico','.ttf','.woff','.woff2','.gif']:cat='aset-biner'
 if f in ['composer.lock','package-lock.json','whatsapp-service/package-lock.json']:cat='lockfile'
 methods=[]
 if cat=='kode':
  for i,l in enumerate(lines,1):
   m=re.search(r'(?:(?:public|private|protected)\s+(?:static\s+)?)?function\s+(\w+)\s*\(',l)
   if not m:m=re.search(r'(?:export\s+)?(?:const|let)\s+(\w+)\s*=\s*(?:async\s*)?\(',l)
   if m:methods.append([m[1],i])
  dig.append(f'\n### {f} ({len(lines)} physical lines)\n'+ '\n'.join(f'{i}: {l}' for i,l in enumerate(lines,1) if re.search(r'function\s|^import |^use |export default|useEffect\(|useState\(|useMemo\(|useCallback\(|Schema::|Route::|->middleware\(|\$table->|protected \$|public \$|const \w+ = \{',l)))
 rows.append(dict(path=f,category=cat,lines=len(lines),sha256=hashlib.sha256(raw).hexdigest(),methods=methods))
(out/'inventory.json').write_text(json.dumps(rows,indent=2));(out/'structural-map.txt').write_text('\n'.join(dig))
with (out/'coverage.csv').open('w') as h:
 w=csv.writer(h);w.writerow(['path','category','physical_lines','status','reason'])
 for r in rows:w.writerow([r['path'],r['category'],r['lines'],'inspeksi struktural otomatis' if r['category']=='kode' else 'inventaris','bukan bukti verifikasi semantik menyeluruh' if r['category']=='kode' else 'aset visual dikecualikan dari refactoring' if r['category']=='aset-biner' else 'konteks lingkungan'])
print(collections.Counter(r['category'] for r in rows));print(out)
