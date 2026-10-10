/**
 * Data Definition: Supported Languages
 * Supported interface languages with complete platform translations.
 */
const SUPPORTED_LANGUAGES = [
  { id: 'en', code: 'en', name: 'English', nativeName: 'English', region: 'Global', flag: '🌐', popular: true, dir: 'ltr' },
  { id: 'hi', code: 'hi', name: 'Hindi', nativeName: 'हिन्दी', region: 'India', flag: '🇮🇳', popular: true, dir: 'ltr' },
  { id: 'gu', code: 'gu', name: 'Gujarati', nativeName: 'ગુજરાતી', region: 'India (Gujarat)', flag: '🇮🇳', popular: true, dir: 'ltr' }
];

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { SUPPORTED_LANGUAGES };
}
