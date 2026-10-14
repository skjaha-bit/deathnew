# Decrypting API responses (React)

User-entered data in `/api/*` responses is now encrypted **field by field**. Any string that starts with `enc:` must be decrypted before you use it. Everything else is plain and can be used as is.

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
    "bank_name": "Landsbankinn",
    "notes_text": "enc:fV0XhOMCPgHDVKGLN3lvHNI6NN2N8nRp5umdjgIKMaeKrm9sormx",
    "bank_vault": "enc:1s53nM1QWstC98+GziAbltQTh+DCIsgjGA+FnNT2+ug=",
    "created_at": "2026-10-14T10:00:00+00:00"
  }
}
```

**Encrypted:** values the user typed or chose in their own records (notes, names, addresses, amounts, yes/no answers, etc.).

**Plain (no decryption needed):**

- reference/lookup lists, such as `/v1/banks`, `/v1/loan-types`, `/v1/countries`, `/v1/insurance-types`, `/v1/trade-unions`, `/v1/funeral-*-types` and `/v1/subscription-plans`
- system content: translations, CMS, banners, notifications, overview and subscriptions
- ids and foreign keys (`id`, `*_id`, `*_ids`)
- reference labels inside user records (`bank_name`, `loan_type_name`, `insurance_company_name`, insurance `types`, …)
- `token`, `created_at`, `updated_at`, `null` values, and `success` / `message` / `errors`

You don't need to remember these rules. Decrypt every string that starts with `enc:` and leave everything else alone. After decryption, each value has its original type: `12.5` comes back as a number and `true` as a boolean.

Request bodies you **send** stay plain JSON, as before.

Algorithm: AES-256-GCM. Each value is `"enc:" + base64( iv[12 bytes] + ciphertext + authTag[16 bytes] )`, and the plaintext is JSON (decrypt, then `JSON.parse`).

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

const PREFIX = 'enc:';
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

export function isEncrypted(value) {
  return typeof value === 'string' && value.startsWith(PREFIX);
}

/** Decrypt one "enc:..." value back to its original type. */
export async function decryptValue(value) {
  const raw = base64ToBytes(value.slice(PREFIX.length));
  const iv = raw.slice(0, IV_LENGTH);
  const cipherAndTag = raw.slice(IV_LENGTH); // Web Crypto expects ciphertext + tag together

  const plain = await crypto.subtle.decrypt(
    { name: 'AES-GCM', iv, tagLength: 128 },
    await getKey(),
    cipherAndTag,
  );

  return JSON.parse(new TextDecoder().decode(plain));
}

/** Recursively decrypt every "enc:" string inside any value. */
export async function decryptData(value) {
  if (isEncrypted(value)) return decryptValue(value);

  if (Array.isArray(value)) {
    return Promise.all(value.map(decryptData));
  }

  if (value !== null && typeof value === 'object') {
    const entries = await Promise.all(
      Object.entries(value).map(async ([k, v]) => [k, await decryptData(v)]),
    );
    return Object.fromEntries(entries);
  }

  return value;
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
- **Turning encryption off while debugging:** set `API_ENCRYPTION_ENABLED=false` in the backend `.env`. Responses then come back fully plain.
