# Decrypting API responses (React)

Every `/api/*` JSON response now has its `data` encrypted **field by field**. You need to decrypt it before using it.

## What a response looks like

```json
{
  "success": true,
  "message": "Success",
  "encrypted": true,
  "data": {
    "id": 12,
    "user_id": 3,
    "bank_id": 5,
    "type_ids": [1, 2],
    "notes_text": "0ZR+zA2K2Agl0hlElx4wQspTXCjeYC+hbZKTnLNnsHh2lO90iKHJ",
    "bank_vault": "qWNIrauFnD3DKGARFyt8GgEVjkMPDZd9xN9tzWh+UzU=",
    "bank": { "id": 5, "name": "UsVRqUxExdLw/4lFfuRPi4ZB3YqG1+dO3Nv+ppmwdmH0dU+j8JxUdZp1" }
  }
}
```

Rules:

- `success`, `message` and validation `errors` are **not** encrypted. Only `data` is, and only when `encrypted` is `true`.
- Keys named `id`, or ending in `_id` or `_ids`, are **plain**: use them as they are.
- `null` stays `null`.
- Every other value (strings, numbers, booleans, dates, pagination fields such as `total`) is an encrypted string. After decryption you get the original type back: `12.5` stays a number and `true` stays a boolean.
- Nested objects and arrays follow the same rules at every level.
- Request bodies you **send** stay plain JSON, as before.

Algorithm: AES-256-GCM. Each value is `base64( iv[12 bytes] + ciphertext + authTag[16 bytes] )`, and the plaintext is JSON (decrypt, then `JSON.parse`).

## Setup

Put the key from the backend `.env` (`API_ENCRYPTION_KEY`) into the frontend env:

```bash
# .env (Vite)
VITE_API_ENCRYPTION_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=
```

With Create React App, use `REACT_APP_API_ENCRYPTION_KEY` and `process.env.REACT_APP_API_ENCRYPTION_KEY` instead.

No npm packages are needed. The code uses the browser's built-in Web Crypto API, which only works on `https://` or `http://localhost`.

## `src/lib/apiCrypto.js`

```js
const RAW_KEY = import.meta.env.VITE_API_ENCRYPTION_KEY; // "base64:...."

const IV_LENGTH = 12;

let keyPromise = null;

function base64ToBytes(b64) {
  return Uint8Array.from(atob(b64), (c) => c.charCodeAt(0));
}

function getKey() {
  if (!keyPromise) {
    const b64 = RAW_KEY.startsWith('base64:') ? RAW_KEY.slice(7) : RAW_KEY;
    keyPromise = crypto.subtle.importKey(
      'raw',
      base64ToBytes(b64),
      { name: 'AES-GCM' },
      false,
      ['decrypt'],
    );
  }
  return keyPromise;
}

// Must match the backend: ids and foreign keys are never encrypted.
function isPlainField(key) {
  return key === 'id' || key.endsWith('_id') || key.endsWith('_ids');
}

/** Decrypt one encrypted value back to its original type. */
export async function decryptValue(payload) {
  const raw = base64ToBytes(payload);
  const iv = raw.slice(0, IV_LENGTH);
  const cipherAndTag = raw.slice(IV_LENGTH); // Web Crypto expects ciphertext + tag together

  const plain = await crypto.subtle.decrypt(
    { name: 'AES-GCM', iv, tagLength: 128 },
    await getKey(),
    cipherAndTag,
  );

  return JSON.parse(new TextDecoder().decode(plain));
}

/** Recursively decrypt a `data` value (object, array or single value). */
export async function decryptData(value, key = null) {
  if (key !== null && isPlainField(key)) return value;

  if (Array.isArray(value)) {
    return Promise.all(value.map((item) => decryptData(item)));
  }

  if (value !== null && typeof value === 'object') {
    const entries = await Promise.all(
      Object.entries(value).map(async ([k, v]) => [k, await decryptData(v, k)]),
    );
    return Object.fromEntries(entries);
  }

  if (typeof value === 'string') return decryptValue(value);

  return value; // null
}

/** Decrypt a full API response body. Bodies without `encrypted: true` are returned unchanged. */
export async function decryptResponse(body) {
  if (!body || body.encrypted !== true) return body;
  return { ...body, data: await decryptData(body.data) };
}
```

## Use it once, globally

### With axios (recommended)

```js
// src/lib/api.js
import axios from 'axios';
import { decryptResponse } from './apiCrypto';

export const api = axios.create({ baseURL: import.meta.env.VITE_API_URL });

api.interceptors.response.use(async (response) => {
  response.data = await decryptResponse(response.data);
  return response;
});
```

From then on, every component gets plain data as usual:

```jsx
const { data } = await api.get('/v1/commercial-banks');
console.log(data.data[0].notes_text); // "My plaintext note"
```

### With fetch

```js
import { decryptResponse } from './apiCrypto';

export async function apiFetch(url, options) {
  const res = await fetch(url, options);
  const body = await res.json();
  return decryptResponse(body);
}
```

## Troubleshooting

- **`OperationError` when decrypting:** the frontend key doesn't match the backend `API_ENCRYPTION_KEY`, or a value was changed in transit.
- **`crypto.subtle` is undefined:** the page isn't on HTTPS or localhost.
- **Turning encryption off while debugging:** set `API_ENCRYPTION_ENABLED=false` in the backend `.env`. Responses then have no `encrypted` flag, and `decryptResponse` returns them unchanged.
