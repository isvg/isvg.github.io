import { getIcon } from "../lib/icon";
import { Buffer } from 'buffer';
export async function get({ params }) {
    const headers = {
        'Content-Type': 'image/svg+xml',
    }
    const icon = Buffer.from(getIcon(params.name), 'base64');
    const body = icon.toString('utf8');
    return {
        headers,
        body: `${body}`,
    }
}