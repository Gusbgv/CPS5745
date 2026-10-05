import sys

# Formulas:
#   pace (minutes per mile) = time (minutes) / distance (miles)
#   speed (mph)             = distance (miles) / (time (minutes) / 60)

def parse_time(text):
    """Accept minutes ("45.5"), mm:ss ("45:30"), or hh:mm:ss ("1:05:30")."""
    parts = text.split(":")
    if len(parts) > 3:
        raise ValueError
    values = [float(p) for p in parts]
    if len(values) == 1:
        return values[0]          # plain minutes
    seconds = 0.0
    for v in values:
        seconds = seconds * 60 + v
    return seconds / 60           # convert total seconds to minutes

def calculate_pace(distance_miles, time_minutes):
    pace = time_minutes / distance_miles
    speed = distance_miles / (time_minutes / 60)
    return pace, speed

def format_pace(pace):
    total_seconds = round(pace * 60)
    return f"{total_seconds // 60}:{total_seconds % 60:02d}"

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Error: Distance and time are required.")
        sys.exit(1)

    try:
        distance = float(sys.argv[1])
        time_minutes = parse_time(sys.argv[2])
    except ValueError:
        print("Error: Enter distance as a number and time as minutes or mm:ss.")
        sys.exit(1)

    if distance <= 0 or time_minutes <= 0:
        print("Error: Distance and time must be greater than zero.")
        sys.exit(1)

    pace, speed = calculate_pace(distance, time_minutes)
    print(f"Pace: {format_pace(pace)} per mile")
    print(f"Speed: {speed:.2f} mph")
