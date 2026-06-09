import { createWriteStream, readdirSync, statSync, readFileSync } from 'fs';
import { join, relative } from 'path';

// Simple ZIP creator using Node built-ins
// We'll write the ZIP format manually

function crc32(buf) {
  let crc = 0xFFFFFFFF;
  const table = new Uint32Array(256);
  for (let i = 0; i < 256; i++) {
    let c = i;
    for (let j = 0; j < 8; j++) c = (c & 1) ? (0xEDB88320 ^ (c >>> 1)) : (c >>> 1);
    table[i] = c;
  }
  for (let i = 0; i < buf.length; i++) crc = table[(crc ^ buf[i]) & 0xFF] ^ (crc >>> 8);
  return (crc ^ 0xFFFFFFFF) >>> 0;
}

function dosDateTime() {
  const d = new Date();
  const date = ((d.getFullYear() - 1980) << 9) | ((d.getMonth() + 1) << 5) | d.getDate();
  const time = (d.getHours() << 11) | (d.getMinutes() << 5) | (d.getSeconds() >> 1);
  return { date, time };
}

function getAllFiles(dir, base = dir) {
  const results = [];
  for (const f of readdirSync(dir)) {
    const full = join(dir, f);
    const rel = relative(base, full);
    if (statSync(full).isDirectory()) {
      results.push(...getAllFiles(full, base));
    } else {
      results.push({ full, rel: rel.replace(/\\/g, '/') });
    }
  }
  return results;
}

function writeUint16LE(buf, offset, v) { buf[offset] = v & 0xFF; buf[offset+1] = (v >> 8) & 0xFF; }
function writeUint32LE(buf, offset, v) {
  buf[offset] = v & 0xFF; buf[offset+1] = (v >> 8) & 0xFF;
  buf[offset+2] = (v >> 16) & 0xFF; buf[offset+3] = (v >> 24) & 0xFF;
}

const srcDir = '/home/runner/workspace/accounting-system';
const outFile = '/home/runner/workspace/jwani-accounting-system.zip';
const prefix = 'accounting-system/';

const files = getAllFiles(srcDir);
const { date, time } = dosDateTime();

const chunks = [];
const centralDir = [];
let offset = 0;

for (const { full, rel } of files) {
  const data = readFileSync(full);
  const name = Buffer.from(prefix + rel, 'utf8');
  const crc = crc32(data);
  const compressed = data; // store uncompressed (method 0)
  const size = data.length;

  // Local file header
  const lhSize = 30 + name.length;
  const lh = Buffer.alloc(lhSize);
  writeUint32LE(lh, 0, 0x04034b50);
  writeUint16LE(lh, 4, 20);       // version needed
  writeUint16LE(lh, 6, 0);        // flags
  writeUint16LE(lh, 8, 0);        // method: store
  writeUint16LE(lh, 10, time);
  writeUint16LE(lh, 12, date);
  writeUint32LE(lh, 14, crc);
  writeUint32LE(lh, 18, size);     // compressed
  writeUint32LE(lh, 22, size);     // uncompressed
  writeUint16LE(lh, 26, name.length);
  writeUint16LE(lh, 28, 0);       // extra
  name.copy(lh, 30);

  chunks.push(lh, compressed);

  // Central directory record
  const cd = Buffer.alloc(46 + name.length);
  writeUint32LE(cd, 0, 0x02014b50);
  writeUint16LE(cd, 4, 20);       // version made
  writeUint16LE(cd, 6, 20);       // version needed
  writeUint16LE(cd, 8, 0);        // flags
  writeUint16LE(cd, 10, 0);       // method
  writeUint16LE(cd, 12, time);
  writeUint16LE(cd, 14, date);
  writeUint32LE(cd, 16, crc);
  writeUint32LE(cd, 20, size);
  writeUint32LE(cd, 24, size);
  writeUint16LE(cd, 28, name.length);
  writeUint16LE(cd, 30, 0);       // extra
  writeUint16LE(cd, 32, 0);       // comment
  writeUint16LE(cd, 34, 0);       // disk start
  writeUint16LE(cd, 36, 0);       // int attr
  writeUint32LE(cd, 38, 0o100644 << 16); // ext attr
  writeUint32LE(cd, 42, offset);  // local header offset
  name.copy(cd, 46);

  centralDir.push(cd);
  offset += lhSize + size;
}

const cdBuf = Buffer.concat(centralDir);
const cdSize = cdBuf.length;
const cdOffset = offset;

// End of central directory
const eocd = Buffer.alloc(22);
writeUint32LE(eocd, 0, 0x06054b50);
writeUint16LE(eocd, 4, 0);
writeUint16LE(eocd, 6, 0);
writeUint16LE(eocd, 8, files.length);
writeUint16LE(eocd, 10, files.length);
writeUint32LE(eocd, 12, cdSize);
writeUint32LE(eocd, 16, cdOffset);
writeUint16LE(eocd, 20, 0);

const final = Buffer.concat([...chunks, cdBuf, eocd]);
import { writeFileSync } from 'fs';
writeFileSync(outFile, final);

const mb = (final.length / 1024 / 1024).toFixed(2);
console.log(`✅ ZIP created: ${outFile} (${mb} MB, ${files.length} files)`);
