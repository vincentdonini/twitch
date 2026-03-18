export class Time {
  static convertSecondsToHms(seconds: number) {
    const hours = Math.floor(seconds / 3600)
    const minutes = Math.floor((seconds % 3600) / 60)
    const remainingSeconds = seconds % 60

    return {
      hours,
      minutes,
      seconds: remainingSeconds,
    }
  }

  static formatSecondsToHms(seconds: number): string {
    const { hours, minutes, seconds: sec } = this.convertSecondsToHms(seconds)
    return `${hours.toString().padStart(2, "0")}:${minutes
      .toString()
      .padStart(2, "0")}:${sec.toString().padStart(2, "0")}`
  }

  static formatSecondsToDynamicHms(seconds: number): string {
    const { hours, minutes, seconds: sec } = this.convertSecondsToHms(seconds);

    const parts = [
      hours > 0 ? `${hours}h` : null,
      minutes > 0 ? `${minutes}min` : null,
      sec > 0 ? `${sec}s` : null,
    ].filter(Boolean);

    return parts.join(' ');
  }
}