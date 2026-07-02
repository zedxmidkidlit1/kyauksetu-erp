# KAI AI Setup

KAI uses the local deterministic responder by default. Keep this default for normal local development, automated tests, and CI:

```ini
KAI_RESPONDER=local
KAI_PROVIDER_ENABLED=false
```

To enable the external Laravel AI SDK responder in local/dev, set placeholder values like these in `.env`:

```ini
KAI_RESPONDER=external
KAI_PROVIDER_ENABLED=true
KAI_AI_PROVIDER=openai
KAI_AI_MODEL=gpt-4o-mini
KAI_PROVIDER_API_KEY=your-local-provider-key
KAI_PROVIDER_ENDPOINT=
KAI_PROVIDER_TIMEOUT=30
```

Do not commit real API keys. Keep secrets in `.env` or your deployment secret store only.

After changing `.env`, clear cached config:

```bash
vendor/bin/sail artisan config:clear
```

Check configuration without sending a provider request:

```bash
vendor/bin/sail artisan kai:smoke
```

Send one tiny local/dev smoke prompt only after external AI is explicitly enabled and configured:

```bash
vendor/bin/sail artisan kai:smoke --send
```

The KAI chat API continues to log only safe metadata: user and assistant message text, context keys, driver, provider, model, status, and request ID metadata. It does not store raw `StudentContextBuilder` output, prompt JSON, API keys, or raw provider exception details.
