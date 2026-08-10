/**
 * Преобразует дату в читаемый вид
 * пример 26 января 2026 г.
 *
 * @param d
 * @param locale
 * @returns {string}
 */
export const formatDate = (d, locale = 'ru-RU') => {

    let splitDate = d.split('.');

    const [day, month, year] = splitDate;

    let date = new Date(year, month - 1, day);

    return date.toLocaleDateString(locale, {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
}
