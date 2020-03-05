interface METAElement extends HTMLElement {
  content: string;
}

export function getCSRFToken (): string {
  // TODO: check if this functions can be stored externally
  const token: METAElement | null = document.querySelector('meta[name=csrf-token]');
  if (token) {
    return token.content;
  }
  throw Error('NO CSRF TOKEN SUPPLIED');
}
