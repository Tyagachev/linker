/**
 * Преобразует имя пользователя в цветной
 * кружок с первой буквой имени по центру
 *
 * @param name
 * @returns {`hsl(${number} 70% 60%)`}
 */
export const avatarColor = (name) => {
    const str = (name || "u").toLowerCase();
    let hash = 0;
    for (let i = 0; i < str.length; i++)
        hash = str.charCodeAt(i) + ((hash << 5) - hash);
    const hue = Math.abs(hash) % 360;
    return `hsl(${hue} 70% 60%)`;
}
