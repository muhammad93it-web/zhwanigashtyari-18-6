import { readdirSync, statSync, readFileSync, writeFileSync } from 'fs';
import { join, relative } from 'path';
import { deflateRawSync } from 'zlib';

// ZIP creator with DEFLATE compression using Node built-ins

function crc32(buf) {
  let crc = 0xFFFFFFFF;
  for (let i = 0; i < buf.length; i++) crc = TABLE[(crc ^ buf[i]) & 0xFF] ^ (crc >>> 8);
  return (crc ^ 0xFFFFFFFF) >>> 0;
}
const TABLE = new Uint32Array(256);
for (let i = 0; i < 256; i++) {
  let c = i;
  for (let j = 0; j < 8; j++) c = (c & 1) ? (0xEDB88320 ^ (c >>> 1)) : (c >>> 1);
  TABLE[i] = c;
}

function dosDateTime() {
  const d = new Date();
  const date = ((d.getFullYear() - 1980) << 9) | ((d.getMonth() + 1) << 5) | d.getDate();
  const time = (d.getHours() << 11) | (d.getMinutes() << 5) | (d.getSeconds() >> 1);
  return { date, time };
}

const EXCLUDE = [
  /(^|\/)\.env$/,
  /(^|\/)\.env\.(?!example$).+$/,
  /(^|\/)\.git(\/|$)/,
  /(^|\/)\.DS_Store$/,
  /(^|\/)node_modules(\/|$)/,
  /(^|\/)bootstrap\/cache\/.*\.php$/,
  /(^|\/)storage\/framework\/(views|cache|sessions)\/(?!\.gitkeep$).+/,
  /(^|\/)storage\/logs\/.+\.log$/,
];

function getAllFiles(dir, base = dir) {
  const results = [];
  for (const f of readdirSync(dir)) {
    const full = join(dir, f);
    const rel = relative(base, full).replace(/\\/g, '/');
    if (EXCLUDE.some((re) => re.test(rel))) continue;
    if (statSync(full).isDirectory()) results.push(...getAllFiles(full, base));
    else results.push({ full, rel });
  }
  return results;
}

function u16(buf, o, v) { buf[o] = v & 0xFF; buf[o+1] = (v >> 8) & 0xFF; }
function u32(buf, o, v) { buf[o] = v & 0xFF; buf[o+1] = (v >> 8) & 0xFF; buf[o+2] = (v >> 16) & 0xFF; buf[o+3] = (v >> 24) & 0xFF; }

const srcDir = '/home/runner/workspace/accounting-system';
const outFile = '/home/runner/workspace/jwani-accounting-system.zip';
const prefix = 'accounting-system/';

const files = getAllFiles(srcDir);
const { date, time } = dosDateTime();

const chunks = [];
const central = [];
let offset = 0;

for (const { full, rel } of files) {
  const data = readFileSync(full);
  const name = Buffer.from(prefix + rel, 'utf8');
  const crc = crc32(data);
  const size = data.length;

  let method = 8;
  let comp = deflateRawSync(data, { level: 6 });
  if (comp.length >= size) { method = 0; comp = data; } // store if no gain
  const csize = comp.length;

  const lh = Buffer.alloc(30 + name.length);
  u32(lh, 0, 0x04034b50);
  u16(lh, 4, 20); u16(lh, 6, 0); u16(lh, 8, method);
  u16(lh, 10, time); u16(lh, 12, date);
  u32(lh, 14, crc); u32(lh, 18, csize); u32(lh, 22, size);
  u16(lh, 26, name.length); u16(lh, 28, 0);
  name.copy(lh, 30);
  chunks.push(lh, comp);

  const cd = Buffer.alloc(46 + name.length);
  u32(cd, 0, 0x02014b50);
  u16(cd, 4, 20); u16(cd, 6, 20); u16(cd, 8, 0); u16(cd, 10, method);
  u16(cd, 12, time); u16(cd, 14, date);
  u32(cd, 16, crc); u32(cd, 20, csize); u32(cd, 24, size);
  u16(cd, 28, name.length); u16(cd, 30, 0); u16(cd, 32, 0);
  u16(cd, 34, 0); u16(cd, 36, 0); u32(cd, 38, 0o100644 << 16);
  u32(cd, 42, offset);
  name.copy(cd, 46);
  central.push(cd);

  offset += lh.length + csize;
}

const cdBuf = Buffer.concat(central);
const eocd = Buffer.alloc(22);
u32(eocd, 0, 0x06054b50);
u16(eocd, 4, 0); u16(eocd, 6, 0);
u16(eocd, 8, files.length); u16(eocd, 10, files.length);
u32(eocd, 12, cdBuf.length); u32(eocd, 16, offset); u16(eocd, 20, 0);

const final = Buffer.concat([...chunks, cdBuf, eocd]);
writeFileSync(outFile, final);
console.log(`✅ ZIP created: ${outFile} (${(final.length/1024/1024).toFixed(2)} MB, ${files.length} files)`);
