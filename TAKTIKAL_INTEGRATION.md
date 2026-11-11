# How the app gets a person's details from Taktikal

The app uses **Taktikal** to confirm who a person is and to fetch their details from Iceland's National Registry (Þjóðskrá).

## Step 1: Connect to Taktikal

- **API address:** `https://onboardingdev.taktikal.is/api` for testing. Production is `https://onboarding.taktikal.is/api`.
- **Credentials from Taktikal:**
  - a **Company Key**, used as the username
  - an **API Key**, used as the password
  - a **Flow Key**, sent with every request
- **Security:** every request uses HTTP Basic Authentication with the Company Key and API Key.

## Step 2: Start the electronic ID login

The person enters their phone number in the app, and the app calls:

```
POST https://onboardingdev.taktikal.is/api/auth/start
```

The person gets an electronic ID (Auðkenni) approval request on their phone.

## Step 3: Wait for approval

The app checks every few seconds until the person approves:

```
POST https://onboardingdev.taktikal.is/api/auth/poll
```

After approval, Taktikal returns the person's **kennitala, name and phone number**.

## Step 4: Get the person's details

The app calls the National Registry lookup with the kennitala:

```
GET https://onboardingdev.taktikal.is/api/nationalRegistry?lookupType=NameAddressFamily&ssn=<kennitala>
```

It returns:

- name and kennitala
- address and postal code
- gender
- citizenship
- family number and partner's kennitala

## Step 5: Check whether the person is deceased

```
POST https://onboardingdev.taktikal.is/api/watchlist/person/
```

The app sends the person's name and birth date. If Taktikal returns a deceased match with the same birth date, the person is treated as deceased:

- **New person:** the account cannot be created.
- **Existing user:** the account is marked deceased and the login is blocked.

## Step 6: Save the data

The details are saved to the user's profile when they register, and refreshed every time they log in.

## Quick lookup without login

The app also has a direct lookup that runs Steps 4 and 5 in one call:

```
GET /api/v1/person-lookup?kennitala=0102851439
```

It returns all available details plus `deceased` (true or false) and `deceased_at`.

## Limitations

- **Email and phone are not available from the National Registry.** The phone number comes from the electronic ID login, and the email is entered by the user when they register.
- **City is not returned,** only the postal code.
- **The deceased check is limited.** Taktikal only reports deaths through its watchlist, which mainly covers politically exposed and sanctioned people. Most ordinary people's deaths are not detected. For full death data, ask Taktikal to add death status to the registry lookup, or connect to Þjóðskrá directly.
